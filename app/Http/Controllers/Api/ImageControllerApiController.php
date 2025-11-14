<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageControllerApiController extends Controller
{


      public function show(Request $request, string $folder, string $filename)
    {
        // Liste des dossiers valides pour les images
        $validFolders = ['logos', 'photos', 'asset', 'users', 'abonnements'];

        // Vérifier que le dossier demandé est valide
        if (!in_array($folder, $validFolders)) {
            abort(404, "Dossier non valide: {$folder}");
        }

        // Construire le chemin complet du fichier
        $filePath = "{$folder}/{$filename}";

        // Vérifier que le fichier existe dans le storage public
        if (!Storage::disk('public')->exists($filePath)) {
            // Essayer avec une autre extension si le fichier n'est pas trouvé
            $possibleExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
            $baseName = pathinfo($filename, PATHINFO_FILENAME);
            $currentExtension = pathinfo($filename, PATHINFO_EXTENSION);

            foreach ($possibleExtensions as $ext) {
                if ($ext !== '.' . $currentExtension) {
                    $alternativeFile = $baseName . $ext;
                    $alternativePath = "{$folder}/{$alternativeFile}";

                    if (Storage::disk('public')->exists($alternativePath)) {
                        $filePath = $alternativePath;
                        $filename = $alternativeFile;
                        break;
                    }
                }
            }

            // Si le fichier n'est toujours pas trouvé
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, "Image non trouvée: {$filePath}");
            }
        }

        // Récupérer le type MIME
        $mimeType = Storage::disk('public')->mimeType($filePath);

        // Servir le fichier avec les headers appropriés
        return Storage::disk('public')->response($filePath, $filename, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
    // public function show(Request $request, string $folder, string $filename)
    // {
    //     // Construire le chemin complet du fichier
    //     $filePath = "{$folder}/{$filename}";

    //     // Vérifier que le fichier existe dans le storage public
    //     if (!Storage::disk('public')->exists($filePath)) {
    //         // Essayer avec un autre chemin si le premier ne fonctionne pas
    //         $alternativePath = "{$folder}s/{$filename}";

    //         if (!Storage::disk('public')->exists($alternativePath)) {
    //             abort(404, "Image non trouvée: {$filePath}");
    //         }

    //         $filePath = $alternativePath;
    //     }

    //     // Récupérer le type MIME
    //     $mimeType = Storage::disk('public')->mimeType($filePath);

    //     // Servir le fichier avec les headers appropriés
    //     return Storage::disk('public')->response($filePath, null, [
    //         'Content-Type' => $mimeType,
    //         'Cache-Control' => 'public, max-age=31536000',
    //     ]);
    // }
}
