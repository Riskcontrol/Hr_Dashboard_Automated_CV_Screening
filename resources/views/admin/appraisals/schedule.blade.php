@extends('layouts.app')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Appraisal Schedule</h1>
        <p class="text-gray-500 mt-1">Annual appraisal timetable — each year has its own schedule</p>
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('admin.appraisal-schedule.index') }}" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
                @if(!in_array(date('Y'), $availableYears))
                    <option value="{{ date('Y') }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ date('Y') }}</option>
                @endif
            </select>
        </form>
        <button onclick="document.getElementById('add-slot-modal').classList.remove('hidden')"
                class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Slot
        </button>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Year:</span> <strong>{{ $year }}</strong></div>
        <div><span class="text-gray-500">Total Slots:</span> <strong>{{ $slots->flatten()->count() }}</strong></div>
        <div><span class="text-gray-500">Morning:</span> <strong>10:00 AM – 11:30 AM</strong></div>
        <div><span class="text-gray-500">Afternoon:</span> <strong>12:00 PM – 4:00 PM</strong></div>
    </div>
</div>

@if($slots->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
        <i class="fas fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
        <p class="text-lg">No schedule yet for {{ $year }}.</p>
        <p class="text-sm mt-1">Click <strong>Add Slot</strong> to build this year's timetable.</p>
    </div>
@else
    @foreach($slots->sortKeys() as $dayNumber => $daySlots)
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="bg-primary-600 text-white px-6 py-3 flex justify-between items-center">
                <h3 class="font-bold text-lg">
                    DAY {{ $dayNumber }}
                    <span class="font-normal text-sm ml-2">
                        {{ $daySlots->first()->scheduled_date->format('l, F j, Y') }}
                    </span>
                </h3>
            </div>

            @php $sessions = $daySlots->groupBy('session'); @endphp

            @foreach(['morning' => 'MORNING SESSION', 'afternoon' => 'AFTERNOON SESSION'] as $sessionKey => $sessionLabel)
                @if($sessions->has($sessionKey))
                    <div class="px-6 py-2 bg-gray-50 border-b border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $sessionLabel }}</p>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase bg-gray-50">
                                <th class="px-6 py-2">S/N</th>
                                <th class="px-6 py-2">Appraisee</th>
                                <th class="px-6 py-2">Time</th>
                                <th class="px-6 py-2">Venue</th>
                                <th class="px-6 py-2">Appraiser</th>
                                <th class="px-6 py-2">Observer(s)</th>
                                <th class="px-6 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sessions[$sessionKey] as $i => $slot)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $slot->appraisee_name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:iA') }} –
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('g:iA') }}
                                </td>
                                <td class="px-6 py-3 text-sm">{{ $slot->venue }}</td>
                                <td class="px-6 py-3 text-sm">{{ $slot->appraiser_name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $slot->observer_names }}</td>
                                <td class="px-6 py-3">
                                    <form method="POST"
                                          action="{{ route('admin.appraisal-schedule.destroy', $slot) }}"
                                          onsubmit="return confirm('Remove this slot?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
        </div>
    @endforeach
@endif

{{-- ADD SLOT MODAL --}}
<div id="add-slot-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 overflow-y-auto" style="max-height:90vh">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-bold text-gray-900">Add Schedule Slot</h2>
            <button onclick="document.getElementById('add-slot-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.appraisal-schedule.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Year</label>
                    <input type="number" name="year" value="{{ $year }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Day Number</label>
                    <input type="number" name="day_number" min="1" max="20" value="1" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Session</label>
                    <select name="session" class="form-select" required>
                        <option value="morning">Morning (10:00 AM – 11:30 AM)</option>
                        <option value="afternoon">Afternoon (12:00 PM – 4:00 PM)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Scheduled Date</label>
                    <input type="date" name="scheduled_date" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" value="10:00" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" value="11:30" class="form-input" required>
                </div>
            </div>

            <div>
                <label class="form-label">Appraisee Name</label>
                <input type="text" name="appraisee_name" class="form-input"
                       placeholder="e.g. Uche Obi" required>
            </div>

            <div>
                <label class="form-label">Link to Staff Profile (optional)</label>
                <select name="appraisee_staff_id" class="form-select">
                    <option value="">— Not linked —</option>
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Linking lets the system match this slot to the staff member's profile.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Appraiser</label>
                    <input type="text" name="appraiser_name" class="form-input" placeholder="e.g. Bukola" required>
                </div>
                <div>
                    <label class="form-label">Observer(s)</label>
                    <input type="text" name="observer_names" class="form-input" placeholder="e.g. Blessing/MD">
                </div>
                <div>
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-input" value="Teams">
                </div>
                <div>
                    <label class="form-label">Prepared By</label>
                    <input type="text" name="prepared_by" class="form-input" placeholder="e.g. Blessing Njoku">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t">
                <button type="button"
                        onclick="document.getElementById('add-slot-modal').classList.add('hidden')"
                        class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Add Slot
                </button>
            </div>
        </form>
    </div>
</div>

@endsection