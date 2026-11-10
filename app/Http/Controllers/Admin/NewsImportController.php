<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsImport;
use App\Models\NewsImportLog;
use App\Models\NewsSource;
use Illuminate\Http\Request;

class NewsImportController extends Controller
{
    public function index(Request $request)
    {
        $sourceId = $request->query('source_id');
        $status = $request->query('status');

        $query = NewsImport::with(['source', 'news.primaryCategory'])
            ->orderByDesc('created_at');

        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }

        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['draft_created', 'published']);
        }

        $imports = $query->paginate(20)->withQueryString();
        $sources = NewsSource::orderBy('name')->get();

        return view('admin.automation.imports.index', compact('imports', 'sources', 'sourceId', 'status'));
    }

    public function duplicates(Request $request)
    {
        $sourceId = $request->query('source_id');

        $query = NewsImport::with(['source', 'duplicateOfNews'])
            ->where('status', 'duplicate_skipped')
            ->orderByDesc('created_at');

        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }

        $duplicates = $query->paginate(20)->withQueryString();
        $sources = NewsSource::orderBy('name')->get();

        return view('admin.automation.imports.duplicates', compact('duplicates', 'sources', 'sourceId'));
    }

    public function logs(Request $request)
    {
        $sourceId = $request->query('source_id');
        $status = $request->query('status');

        $query = NewsImportLog::with('source')->orderByDesc('created_at');

        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $logs = $query->paginate(25)->withQueryString();
        $sources = NewsSource::orderBy('name')->get();

        return view('admin.automation.logs.index', compact('logs', 'sources', 'sourceId', 'status'));
    }
}
