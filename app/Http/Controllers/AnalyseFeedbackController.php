<?php

namespace App\Http\Controllers;
use App\Services\AnalyseFeedbackService;
use Illuminate\Http\Request;

class AnalyseFeedbackController extends Controller
{
    //
    protected $analyseService;

    public function __construct(AnalyseFeedbackService $analyseService)
    {
        $this->analyseService = $analyseService;
    }

    public function dashboard()
    {
        $analyse = $this->analyseService->analyserTendances();

        return view('admin.feedback.dashboard', compact('analyse'));
    }

    public function apiAnalyse(Request $request)
    {
        $limit = $request->get('limit', 100);
        $analyse = $this->analyseService->analyserTendances($limit);

        return response()->json([
            'success' => true,
            'data' => $analyse
        ]);
    }

    public function rapportComplet()
    {
        $rapport = $this->analyseService->genererRapportComplet();

        return response()->json([
            'success' => true,
            'data' => $rapport
        ]);
    }

    public function statistiques()
    {
        $stats = $this->analyseService->getStatistiquesNotes();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
