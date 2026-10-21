@extends('admin.navigation')

@section('content')

    <?php
    use App\Models\Section;
    use App\Http\Controllers\CommonController;

    $user = Auth()->user();
    ?>

    <style>
        .student-id-card-item {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border-radius: 16px;
            overflow: hidden;
        }

        .student-id-card-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(18, 63, 128, 0.12) !important;
        }

        .card-banner-bg {
            height: 64px;
            background: linear-gradient(135deg, #002050 0%, #123f80 60%, #2563eb 100%);
            position: relative;
        }

        .avatar-wrapper {
            position: relative;
            margin-top: -36px;
            margin-bottom: 10px;
        }

        .avatar-img {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 14px rgba(0, 32, 80, 0.18);
            background: #ffffff;
        }

        .info-pill-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 10px 12px;
            margin-top: 12px;
            width: 100%;
        }

        .info-pill-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            padding: 3px 0;
        }

        .info-pill-label {
            color: #64748b;
            font-weight: 600;
        }

        .info-pill-value {
            color: #0f172a;
            font-weight: 700;
        }
    </style>

    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Student ID Card') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="{{ route('admin.student') }}">{{ get_phrase('Students') }}</a></li>
                            <li><a href="#">{{ get_phrase('ID Card') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <div
                    class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                    <form action="{{ route('admin.student.id_card_filter') }}" method="GET" class="w-100">
                        <div class="row g-3 align-items-end">
                            <!-- Search -->
                            <div class="col-md-4">
                                <label class="eForm-label">{{ get_phrase('Search by Name / Email') }}</label>
                                <div class="search-input d-flex justify-content-start align-items-center">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                            <path id="Search_icon" data-name="Search icon"
                                                d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                                                fill="#797c8b" />
                                        </svg>
                                    </span>
                                    <input type="text" name="search" value="{{ $search }}"
                                        placeholder="{{ get_phrase('Search students...') }}" class="form-control" />
                                </div>
                            </div>

                            <!-- Class -->
                            <div class="col-md-3">
                                <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
                                <select class="form-select" name="class_id" id="class_id"
                                    onchange="classWiseSectionIdCard(this.value)">
                                    <option value="">{{ get_phrase('All Classes') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ $class_id == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Section -->
                            <div class="col-md-3">
                                <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
                                <select class="form-select" name="section_id" id="section_id">
                                    @if ($class_id != '')
                                        <?php    $sections = Section::get()->where('class_id', $class_id); ?>
                                        <option value="">{{ get_phrase('All Sections') }}</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}" {{ $section_id == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">{{ get_phrase('First select a class') }}</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Apply Button -->
                            <div class="col-md-2">
                                <button type="submit" class="eBtn eBtn btn-primary w-100">
                                    {{ get_phrase('Search') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    @if ($students === null)
        <!-- Initial state: no search performed -->
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2">
                    <div class="empty_box center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none"
                            stroke="#c0c5d0" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"
                            class="mb-3 d-block mx-auto">
                            <rect x="2" y="4" width="20" height="16" rx="3" />
                            <circle cx="9" cy="10" r="2.5" />
                            <path d="M5 19c0-2.21 1.79-4 4-4h2" />
                            <line x1="15" y1="9" x2="19" y2="9" />
                            <line x1="15" y1="13" x2="18" y2="13" />
                        </svg>
                        <p class="text-muted mt-2">
                            {{ get_phrase('Select a class or search to find students and generate their ID cards.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif (count($students) === 0)
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2">
                    <div class="empty_box center py-5">
                        <img class="mb-3" width="120px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span>{{ get_phrase('No students found matching your criteria.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Student Cards Grid -->
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gr-10">
                        <p class="admin-tInfo mb-0">
                            {{ get_phrase('Found') }} <strong>{{ count($students) }}</strong> {{ get_phrase('student(s)') }}
                        </p>
                    </div>

                    <div class="row g-3">
                        @foreach ($students as $enrollment)
                                    <?php
                            $student = DB::table('users')->where('id', $enrollment->user_id)->first();
                            if (!$student)
                                continue;

                            $user_image = get_user_image($enrollment->user_id);
                            $info = json_decode($student->user_information ?? '{}');
                            $stu_details = (new CommonController)->get_student_academic_info($student->id);

                            $blood_group = !empty($info->blood_group) ? strtoupper($info->blood_group) : '';
                            $phone = !empty($info->phone) ? $info->phone : '';
                            $address = !empty($info->address) ? $info->address : '';
                                            ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                                        <div class="student-id-card-item card h-100 border-0 shadow-sm background-white">
                                            <!-- Top Decorative Gradient Banner -->
                                            <div class="card-banner-bg"></div>

                                            <div class="card-body d-flex flex-column align-items-center text-center p-3 pt-0">
                                                <!-- Avatar -->
                                                <div class="avatar-wrapper">
                                                    <img src="{{ $user_image }}" class="avatar-img" alt="{{ $student->name }}">
                                                </div>

                                                <!-- Name -->
                                                <h6 class="mb-1 fw-bold" style="font-size:15px; color:#0f172a; line-height:1.2;">
                                                    {{ $student->name }}</h6>

                                                <!-- Class & Section -->
                                                <p class="mb-1 fw-semibold" style="font-size:12px; color:#475569;">
                                                    @if (!empty($stu_details['class_name']))
                                                        {{ $stu_details['class_name'] }}
                                                        @if (!empty($stu_details['section_name']))
                                                            &bull; {{ $stu_details['section_name'] }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">{{ get_phrase('No class assigned') }}</span>
                                                    @endif
                                                </p>

                                                <!-- ID Code Badge -->
                                                @if (!empty($student->code))
                                                    <span
                                                        style="background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:700; padding:4px 10px; border-radius:12px; border:1px solid #dbeafe;">
                                                        ID: {{ $student->code }}
                                                    </span>
                                                @endif

                                                <!-- Quick Details Box -->
                                                <div class="info-pill-box">
                                                    @if ($blood_group)
                                                        <div class="info-pill-row">
                                                            <span class="info-pill-label">{{ get_phrase('Blood Group') }}:</span>
                                                            <span class="bg-danger text-white fw-bold"
                                                                style="font-size:10px; padding:3px 8px;">{{ $blood_group }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($phone)
                                                        <div class="info-pill-row">
                                                            <span class="info-pill-label">{{ get_phrase('Phone') }}:</span>
                                                            <span class="info-pill-value">{{ $phone }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($address)
                                                        <div class="info-pill-row">
                                                            <span class="info-pill-label">{{ get_phrase('Address') }}:</span>
                                                            <span class="info-pill-value text-truncate" style="max-width:120px;"
                                                                title="{{ $address }}">{{ $address }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Action Footer -->
                                            <div class="card-footer bg-transparent border-0 text-center pb-3 pt-0">
                                                <button type="button"
                                                    onclick="largeModal('{{ route('admin.student.id_card', ['id' => $student->id]) }}', '{{ get_phrase('Student ID Card') }}')"
                                                    class="eBtn eBtn btn-primary w-100 d-flex align-items-center justify-content-center"
                                                    style="font-size:13px; font-weight:600; padding:9px 16px; border-radius:10px; background:linear-gradient(135deg, #123f80 0%, #2563eb 100%); border:none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                                        fill="currentColor" class="me-2">
                                                        <path
                                                            d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zM8.5 14a2.5 2.5 0 110-5 2.5 2.5 0 010 5zm-3 3c0-1.66 1.34-3 3-3s3 1.34 3 3H5.5zm9-2h4v-1.5h-4V15zm0-3h4v-1.5h-4V12zm0-3h4V7.5h-4V9z" />
                                                    </svg>
                                                    {{ get_phrase('Generate ID Card') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        "use strict";

        function classWiseSectionIdCard(classId) {
            if (!classId) {
                document.getElementById('section_id').innerHTML =
                    '<option value="">{{ get_phrase('First select a class') }}</option>';
                return;
            }
            let url = "{{ route('class_wise_sections', ['id' => ':classId']) }}";
            url = url.replace(':classId', classId);
            $.ajax({
                url: url,
                success: function (response) {
                    document.getElementById('section_id').innerHTML =
                        '<option value="">{{ get_phrase('All Sections') }}</option>' + response;
                }
            });
        }
    </script>

@endsection