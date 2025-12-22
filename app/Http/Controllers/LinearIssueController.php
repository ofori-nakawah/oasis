<?php

namespace App\Http\Controllers;

use App\Services\LinearService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class LinearIssueController extends Controller
{
    protected LinearService $linearService;

    public function __construct(LinearService $linearService)
    {
        $this->linearService = $linearService;
    }

    /**
     * Create a Linear issue
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Example request payload:
     * {
     *   "team_id": "uuid-here",
     *   "title": "Issue Title",
     *   "description": "Issue description (optional)"
     * }
     *
     * Example success response:
     * {
     *   "status": true,
     *   "message": "Issue created successfully",
     *   "data": {
     *     "id": "uuid-here",
     *     "identifier": "VT-123",
     *     "url": "https://linear.app/vorktech/issue/VT-123/...",
     *     "title": "Issue Title",
     *     "description": "Issue description",
     *     "state": "Todo",
     *     "priority": 0,
     *     "created_at": "2025-12-22T15:30:00.000Z"
     *   }
     * }
     *
     * Example error response:
     * {
     *   "status": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "team_id": ["The team id field is required."]
     *   }
     * }
     */
    public function createIssue(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:4', // 0 = No priority, 1 = Urgent, 2 = High, 3 = Normal, 4 = Low
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $teamId = $request->input('team_id');
            $title = $request->input('title');
            $description = $request->input('description');

            // Build additional data
            $additionalData = [];
            
            if ($request->has('priority')) {
                $additionalData['priority'] = (int) $request->input('priority');
            }

            if ($request->has('label_ids') && is_array($request->input('label_ids'))) {
                $additionalData['labelIds'] = $request->input('label_ids');
            }

            $issue = $this->linearService->createIssue(
                $teamId,
                $title,
                $description,
                $additionalData
            );

            return response()->json([
                'status' => true,
                'message' => 'Issue created successfully',
                'data' => $issue,
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to create Linear issue via API', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create Linear issue: ' . $e->getMessage(),
            ], 500);
        }
    }
}

