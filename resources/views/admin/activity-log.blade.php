@extends('admin.layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Activity Log</li>
                    </ol>
                </div>
                <h4 class="page-title">Activity Log</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="header-title">Admin Activities</h4>
                            <p class="text-muted mb-0">Track and monitor all admin activities.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-end">
                                <div class="btn-group mb-2
                                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter me-1"></i> Filter 
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}">All Activities</a>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'login']) }}">Logins</a>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'profile']) }}">Profile Updates</a>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'settings']) }}">Settings Changes</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'today']) }}">Today</a>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'week']) }}">This Week</a>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['filter' => 'month']) }}">This Month</a>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            
                            <a href="{{ route('admin.activity.log') }}" class="btn btn-light mb-2">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Event</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $activity->event_color }} bg-opacity-10 text-{{ $activity->event_color }}">
                                                {{ ucfirst($activity->event_name) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-body fw-semibold">{{ $activity->description }}</span>
                                            @if($activity->properties->has('attributes'))
                                                <div class="text-muted small mt-1">
                                                    @foreach($activity->properties->get('attributes') as $key => $value)
                                                        <div>{{ ucfirst($key) }}: {{ is_array($value) ? json_encode($value) : $value }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $activity->properties->get('ip_address') }}</td>
                                        <td>
                                            <span class="d-inline-block text-truncate" style="max-width: 200px;" 
                                                  data-bs-toggle="tooltip" title="{{ $activity->properties->get('user_agent') }}">
                                                {{ $activity->properties->get('user_agent') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span data-bs-toggle="tooltip" title="{{ $activity->created_at->format('M d, Y h:i A') }}">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-light" data-bs-toggle="modal" 
                                                    data-bs-target="#activityModal{{ $activity->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Activity Details Modal -->
                                    <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Activity Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h6>Event</h6>
                                                            <p>
                                                                <span class="badge bg-{{ $activity->event_color }} bg-opacity-10 text-{{ $activity->event_color }}">
                                                                    {{ ucfirst($activity->event_name) }}
                                                                </span>
                                                            </p>
                                                            
                                                            <h6>Description</h6>
                                                            <p>{{ $activity->description }}</p>
                                                            
                                                            <h6>Date & Time</h6>
                                                            <p>{{ $activity->created_at->format('F j, Y h:i A') }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>IP Address</h6>
                                                            <p>
                                                                {{ $activity->properties->get('ip_address') }}
                                                                <a href="#" class="ms-2" data-bs-toggle="tooltip" title="Lookup IP">
                                                                    <i class="fas fa-search"></i>
                                                                </a>
                                                            </p>
                                                            
                                                            <h6>User Agent</h6>
                                                            <p>{{ $activity->properties->get('user_agent') }}</p>
                                                            
                                                            <h6>Location</h6>
                                                            <p>
                                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                                {{ $activity->properties->get('location', 'Unknown') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($activity->properties->has('attributes') || $activity->properties->has('old'))
                                                        <div class="border-top pt-3 mt-3">
                                                            <h6>Details</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Attribute</th>
                                                                            <th>Old Value</th>
                                                                            <th>New Value</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $oldValues = $activity->properties->get('old', []);
                                                                            $newValues = $activity->properties->get('attributes', []);
                                                                            $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                                                                        @endphp
                                                                        
                                                                        @foreach($allKeys as $key)
                                                                            <tr>
                                                                                <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                                <td>
                                                                                    @if(array_key_exists($key, $oldValues))
                                                                                        @if(is_array($oldValues[$key]))
                                                                                            <pre class="mb-0">{{ json_encode($oldValues[$key], JSON_PRETTY_PRINT) }}</pre>
                                                                                        @else
                                                                                            {{ $oldValues[$key] ?? '-' }}
                                                                                        @endif
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if(array_key_exists($key, $newValues))
                                                                                        @if(is_array($newValues[$key]))
                                                                                            <pre class="mb-0">{{ json_encode($newValues[$key], JSON_PRETTY_PRINT) }}</pre>
                                                                                        @else
                                                                                            {{ $newValues[$key] ?? '-' }}
                                                                                        @endif
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">
                                                        <i class="fas fa-undo me-1"></i> Revert Changes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-2"></i>
                                                <h5>No activities found</h5>
                                                <p class="mb-0">There are no activities to display.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="text-muted">
                                Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                {{ $activities->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.activity.export') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="csv">CSV (Comma Separated Values)</option>
                            <option value="xlsx">Excel (XLSX)</option>
                            <option value="pdf">PDF Document</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportDateRange" class="form-label">Date Range</label>
                        <select class="form-select" id="exportDateRange" name="date_range">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    
                    <div class="row mb-3" id="customDateRange" style="display: none;">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Columns to Export</label>
                        <div class="border rounded p-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="colEvent" name="columns[]" value="event" checked>
                                <label class="form-check-label" for="colEvent">Event</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="colDescription" name="columns[]" value="description" checked>
                                <label class="form-check-label" for="colDescription">Description</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="colIp" name="columns[]" value="ip_address" checked>
                                <label class="form-check-label" for="colIp">IP Address</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="colUserAgent" name="columns[]" value="user_agent">
                                <label class="form-check-label" for="colUserAgent">User Agent</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="colDate" name="columns[]" value="created_at" checked>
                                <label class="form-check-label" for="colDate">Date & Time</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide custom date range
        const dateRangeSelect = document.getElementById('exportDateRange');
        const customDateRange = document.getElementById('customDateRange');
        
        if (dateRangeSelect && customDateRange) {
            dateRangeSelect.addEventListener('change', function() {
                customDateRange.style.display = this.value === 'custom' ? 'flex' : 'none';
            });
        }
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
