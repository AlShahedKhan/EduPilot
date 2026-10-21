@php
    // Ensure student details is an object for clean property access
    $sd = (object) $student_details;

    $school_id = auth()->check() ? auth()->user()->school_id : ($sd->school_id ?? 1);
    $school_data = DB::table('schools')->where('id', $school_id)->first();

    $school_name = $school_data->title ?? get_settings('system_title') ?? 'School Name';
    $school_address = $school_data->address ?? '';
    $school_phone = $school_data->phone ?? '';
    $school_email = $school_data->email ?? '';

    $school_logo_file = $school_data->school_logo ?? '';
    if (!empty($school_logo_file) && file_exists(public_path('assets/uploads/school_logo/' . $school_logo_file))) {
        $school_logo = asset('assets/uploads/school_logo/' . $school_logo_file);
    } else {
        $school_logo = asset('assets/images/id_logo.png');
    }

    $student_photo = !empty($sd->photo) ? $sd->photo : asset('assets/uploads/user-images/thumbnail.png');
    $student_name = $sd->name ?? 'Student Name';
    $student_code = !empty($sd->code) ? $sd->code : 'N/A';
    $class_name = !empty($sd->class_name) ? $sd->class_name : 'N/A';
    $section_name = !empty($sd->section_name) ? $sd->section_name : 'N/A';
    $blood_group = !empty($sd->blood_group) ? strtoupper($sd->blood_group) : 'N/A';
    $phone = !empty($sd->phone) ? $sd->phone : 'N/A';
    $email = !empty($sd->email) ? $sd->email : 'N/A';
    $address = !empty($sd->address) ? $sd->address : 'N/A';
    $parent_name = !empty($sd->parent_name) ? $sd->parent_name : 'N/A';
    $session = !empty($sd->running_session) ? $sd->running_session : date('Y');
@endphp

<style>
    /* ── Modal Dialog Expansion ── */
    #large-modal .modal-dialog {
        max-width: 960px !important;
    }

    /* ── Controller Toolbar ── */
    .idcard-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f1f5f9;
        padding: 10px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .idcard-btn-group {
        display: flex;
        gap: 6px;
        background: #e2e8f0;
        padding: 4px;
        border-radius: 8px;
    }

    .idcard-toggle-btn {
        border: none;
        background: transparent;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .idcard-toggle-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    /* ── Container for ID Cards ── */
    .idcard-stage {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
        padding: 10px 0;
    }

    /* =========================================================
       VERTICAL ORIENTATION STYLES (Default)
       ========================================================= */
    .idc-card-vert {
        width: 290px;
        height: 450px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
        border: 1px solid #e2e8f0;
        box-sizing: border-box;
    }

    /* Watermark background */
    .idc-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 170px;
        height: 170px;
        opacity: 0.08;
        background-image: url('{{ $school_logo }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        pointer-events: none;
        z-index: 1;
    }

    /* Header */
    .idc-header-vert {
        background: linear-gradient(135deg, #002050 0%, #123f80 100%);
        color: #ffffff;
        padding: 14px 12px;
        text-align: center;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .idc-header-logo-vert {
        width: 42px;
        height: 42px;
        object-fit: contain;
        background: #ffffff;
        border-radius: 8px;
        padding: 3px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .idc-header-text-vert {
        text-align: left;
        flex-grow: 1;
        overflow: hidden;
    }

    .idc-school-name-vert {
        font-size: 13px;
        font-weight: 700;
        line-height: 1.2;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .idc-card-sub-vert {
        font-size: 9.5px;
        color: #93c5fd;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        font-weight: 600;
    }

    /* Gold Stripe */
    .idc-gold-stripe {
        height: 3px;
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #f59e0b 100%);
        z-index: 2;
    }

    /* Body Front */
    .idc-body-vert {
        padding: 16px 14px;
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-grow: 1;
    }

    .idc-photo-ring-vert {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 3px solid #123f80;
        padding: 2px;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(18, 63, 128, 0.18);
        overflow: hidden;
        margin-bottom: 10px;
    }

    .idc-photo-ring-vert img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .idc-stu-name-vert {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
        text-align: center;
        line-height: 1.2;
    }

    .idc-stu-code-vert {
        font-size: 11px;
        font-weight: 700;
        color: #1e40af;
        background: #dbeafe;
        padding: 2px 10px;
        border-radius: 12px;
        margin-bottom: 12px;
    }

    .idc-info-list-vert {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .idc-info-item-vert {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11.5px;
        padding: 4px 8px;
        background: rgba(241, 245, 249, 0.7);
        border-radius: 6px;
    }

    .idc-info-lbl-vert {
        font-weight: 600;
        color: #64748b;
    }

    .idc-info-val-vert {
        font-weight: 700;
        color: #1e293b;
        text-align: right;
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Footer Vert */
    .idc-footer-vert {
        background: #002050;
        color: #ffffff;
        padding: 8px 12px;
        text-align: center;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        z-index: 2;
        margin-top: auto;
    }

    /* Back side specific vert */
    .idc-back-title-vert {
        font-size: 12px;
        font-weight: 800;
        color: #002050;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        text-align: center;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 4px;
        width: 100%;
    }

    .idc-sig-area-vert {
        margin-top: auto;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding-top: 10px;
    }

    .idc-sig-box {
        text-align: center;
    }

    .idc-sig-line {
        width: 80px;
        border-top: 1px dashed #94a3b8;
        margin-bottom: 2px;
    }

    .idc-sig-txt {
        font-size: 8.5px;
        color: #64748b;
        font-weight: 600;
    }

    /* =========================================================
       HORIZONTAL ORIENTATION STYLES
       ========================================================= */
    .idc-card-horiz {
        width: 440px;
        height: 275px;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
        border: 1px solid #e2e8f0;
        box-sizing: border-box;
    }

    .idc-card-horiz .idc-watermark {
        width: 160px;
        height: 160px;
        opacity: 0.07;
    }

    .idc-header-horiz {
        background: linear-gradient(135deg, #002050 0%, #123f80 100%);
        color: #ffffff;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 2;
    }

    .idc-header-logo-horiz {
        width: 36px;
        height: 36px;
        object-fit: contain;
        background: #ffffff;
        border-radius: 6px;
        padding: 2px;
        flex-shrink: 0;
    }

    .idc-school-name-horiz {
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
    }

    .idc-card-sub-horiz {
        font-size: 9px;
        color: #93c5fd;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .idc-body-horiz {
        padding: 12px 14px;
        display: flex;
        gap: 14px;
        flex-grow: 1;
        z-index: 2;
        align-items: center;
    }

    .idc-left-horiz {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 110px;
        flex-shrink: 0;
    }

    .idc-photo-ring-horiz {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        border: 2px solid #123f80;
        padding: 2px;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 6px;
    }

    .idc-photo-ring-horiz img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .idc-stu-code-horiz {
        font-size: 10px;
        font-weight: 700;
        color: #1e40af;
        background: #dbeafe;
        padding: 2px 8px;
        border-radius: 10px;
        text-align: center;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .idc-right-horiz {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
        overflow: hidden;
    }

    .idc-stu-name-horiz {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .idc-grid-horiz {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px 8px;
    }

    .idc-info-item-horiz {
        font-size: 11px;
        background: rgba(241, 245, 249, 0.75);
        padding: 3px 6px;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
    }

    .idc-info-lbl-horiz {
        font-size: 8.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .idc-info-val-horiz {
        font-size: 10.5px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .idc-footer-horiz {
        background: #002050;
        color: #ffffff;
        padding: 6px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 9.5px;
        font-weight: 600;
        z-index: 2;
        margin-top: auto;
    }

    /* Print media rule */
    @media print {
        body * {
            visibility: hidden;
        }

        #printableArea,
        #printableArea * {
            visibility: visible;
        }

        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .idcard-controls {
            display: none !important;
        }

        .idcard-stage {
            gap: 20px !important;
            padding: 0 !important;
        }

        .idc-card-vert,
        .idc-card-horiz {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            page-break-inside: avoid;
        }
    }
</style>

<!-- Top Controls -->
<div class="idcard-controls d-print-none">
    <div class="d-flex align-items-center gap-2">
        <span class="fw-bold text-secondary" style="font-size: 13px;">{{ get_phrase('Orientation') }}:</span>
        <div class="idcard-btn-group">
            <button type="button" class="idcard-toggle-btn active" id="btn-vertical"
                onclick="setOrientation('vertical')">
                <i class="bi bi-phone me-1"></i> {{ get_phrase('Vertical') }}
            </button>
            <button type="button" class="idcard-toggle-btn" id="btn-horizontal" onclick="setOrientation('horizontal')">
                <i class="bi bi-credit-card me-1"></i> {{ get_phrase('Horizontal') }}
            </button>
        </div>
    </div>

    <div>
        <button type="button" class="eBtn eBtn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> {{ get_phrase('Print ID Cards') }}
        </button>
    </div>
</div>

<!-- Main Printable Stage -->
<div id="printableArea">
    <!-- VERTICAL VIEW (Default) -->
    <div id="view-vertical" class="idcard-stage">

        <!-- FRONT SIDE (Vertical) -->
        <div class="idc-card-vert">
            <div class="idc-watermark"></div>
            <div class="idc-header-vert">
                <img class="idc-header-logo-vert" src="{{ $school_logo }}" alt="Logo">
                <div class="idc-header-text-vert">
                    <div class="idc-school-name-vert" title="{{ $school_name }}">{{ $school_name }}</div>
                    <div class="idc-card-sub-vert">{{ get_phrase('Student ID Card') }}</div>
                </div>
            </div>
            <div class="idc-gold-stripe"></div>

            <div class="idc-body-vert">
                <div class="idc-photo-ring-vert">
                    <img src="{{ $student_photo }}" alt="{{ $student_name }}">
                </div>
                <div class="idc-stu-name-vert">{{ $student_name }}</div>
                <div class="idc-stu-code-vert">ID: {{ $student_code }}</div>

                <div class="idc-info-list-vert">
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Class') }}:</span>
                        <span class="idc-info-val-vert">{{ $class_name }}</span>
                    </div>
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Section') }}:</span>
                        <span class="idc-info-val-vert">{{ $section_name }}</span>
                    </div>
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Blood Group') }}:</span>
                        <span class="idc-info-val-vert text-danger">{{ $blood_group }}</span>
                    </div>
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Session') }}:</span>
                        <span class="idc-info-val-vert">{{ $session }}</span>
                    </div>
                </div>
            </div>

            <div class="idc-footer-vert">
                {{ get_phrase('OFFICIAL IDENTITY CARD') }}
            </div>
        </div>

        <!-- BACK SIDE (Vertical) -->
        <div class="idc-card-vert">
            <div class="idc-watermark"></div>
            <div class="idc-header-vert">
                <img class="idc-header-logo-vert" src="{{ $school_logo }}" alt="Logo">
                <div class="idc-header-text-vert">
                    <div class="idc-school-name-vert" title="{{ $school_name }}">{{ $school_name }}</div>
                    <div class="idc-card-sub-vert">{{ get_phrase('Student Information') }}</div>
                </div>
            </div>
            <div class="idc-gold-stripe"></div>

            <div class="idc-body-vert">
                <div class="idc-back-title-vert">{{ get_phrase('Contact & Details') }}</div>

                <div class="idc-info-list-vert">
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Phone') }}:</span>
                        <span class="idc-info-val-vert">{{ $phone }}</span>
                    </div>
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Email') }}:</span>
                        <span class="idc-info-val-vert" title="{{ $email }}">{{ $email }}</span>
                    </div>
                    <div class="idc-info-item-vert">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Parent') }}:</span>
                        <span class="idc-info-val-vert">{{ $parent_name }}</span>
                    </div>
                    <div class="idc-info-item-vert" style="align-items: flex-start;">
                        <span class="idc-info-lbl-vert">{{ get_phrase('Address') }}:</span>
                        <span class="idc-info-val-vert"
                            style="white-space: normal; text-align: right; max-width: 150px;">{{ $address }}</span>
                    </div>
                </div>

                <div class="idc-sig-area-vert">
                    <div class="idc-sig-box">
                        <div class="idc-sig-line"></div>
                        <div class="idc-sig-txt">{{ get_phrase('Holder Signature') }}</div>
                    </div>
                    <div class="idc-sig-box">
                        <div class="idc-sig-line"></div>
                        <div class="idc-sig-txt">{{ get_phrase('Principal Signature') }}</div>
                    </div>
                </div>
            </div>

            <div class="idc-footer-vert">
                {{ get_phrase('If found, please return to school office') }}
            </div>
        </div>

    </div>

    <!-- HORIZONTAL VIEW (Hidden by default) -->
    <div id="view-horizontal" class="idcard-stage" style="display: none;">

        <!-- FRONT SIDE (Horizontal) -->
        <div class="idc-card-horiz">
            <div class="idc-watermark"></div>
            <div class="idc-header-horiz">
                <img class="idc-header-logo-horiz" src="{{ $school_logo }}" alt="Logo">
                <div>
                    <div class="idc-school-name-horiz">{{ $school_name }}</div>
                    <div class="idc-card-sub-horiz">{{ get_phrase('Student Identity Card') }}</div>
                </div>
            </div>
            <div class="idc-gold-stripe"></div>

            <div class="idc-body-horiz">
                <div class="idc-left-horiz">
                    <div class="idc-photo-ring-horiz">
                        <img src="{{ $student_photo }}" alt="{{ $student_name }}">
                    </div>
                    <div class="idc-stu-code-horiz">ID: {{ $student_code }}</div>
                </div>

                <div class="idc-right-horiz">
                    <div class="idc-stu-name-horiz">{{ $student_name }}</div>
                    <div class="idc-grid-horiz">
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Class') }}</span>
                            <span class="idc-info-val-horiz">{{ $class_name }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Section') }}</span>
                            <span class="idc-info-val-horiz">{{ $section_name }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Blood Group') }}</span>
                            <span class="idc-info-val-horiz text-danger">{{ $blood_group }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Session') }}</span>
                            <span class="idc-info-val-horiz">{{ $session }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="idc-footer-horiz">
                <span>{{ get_phrase('OFFICIAL STUDENT CARD') }}</span>
                <span>{{ $session }}</span>
            </div>
        </div>

        <!-- BACK SIDE (Horizontal) -->
        <div class="idc-card-horiz">
            <div class="idc-watermark"></div>
            <div class="idc-header-horiz">
                <img class="idc-header-logo-horiz" src="{{ $school_logo }}" alt="Logo">
                <div>
                    <div class="idc-school-name-horiz">{{ $school_name }}</div>
                    <div class="idc-card-sub-horiz">{{ get_phrase('Student Information & Emergency Contact') }}</div>
                </div>
            </div>
            <div class="idc-gold-stripe"></div>

            <div class="idc-body-horiz" style="align-items: stretch;">
                <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div class="idc-grid-horiz" style="grid-template-columns: 1fr 1fr;">
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Phone') }}</span>
                            <span class="idc-info-val-horiz">{{ $phone }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Email') }}</span>
                            <span class="idc-info-val-horiz" title="{{ $email }}">{{ $email }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Parent') }}</span>
                            <span class="idc-info-val-horiz">{{ $parent_name }}</span>
                        </div>
                        <div class="idc-info-item-horiz">
                            <span class="idc-info-lbl-horiz">{{ get_phrase('Address') }}</span>
                            <span class="idc-info-val-horiz" title="{{ $address }}">{{ $address }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end pt-2">
                        <div class="idc-sig-box">
                            <div class="idc-sig-line"></div>
                            <div class="idc-sig-txt">{{ get_phrase('Holder Signature') }}</div>
                        </div>
                        <div class="idc-sig-box">
                            <div class="idc-sig-line"></div>
                            <div class="idc-sig-txt">{{ get_phrase('Authorized Signature') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="idc-footer-horiz">
                <span>{{ get_phrase('If found, please return to school office') }}</span>
                <span>{{ $school_phone }}</span>
            </div>
        </div>

    </div>
</div>

<script>
    "use strict";

    function setOrientation(mode) {
        var vertView = document.getElementById('view-vertical');
        var horizView = document.getElementById('view-horizontal');
        var btnVert = document.getElementById('btn-vertical');
        var btnHoriz = document.getElementById('btn-horizontal');

        if (mode === 'horizontal') {
            vertView.style.display = 'none';
            horizView.style.display = 'flex';
            btnVert.classList.remove('active');
            btnHoriz.classList.add('active');
        } else {
            vertView.style.display = 'flex';
            horizView.style.display = 'none';
            btnVert.classList.add('active');
            btnHoriz.classList.remove('active');
        }
    }
</script>