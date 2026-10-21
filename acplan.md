# PDF Generation Standard

## Implementation Status

Phases 1 through 7 are now wired to the tenant admin application with dedicated typed controllers/policies, tenant-scoped models and relationships, focused request validation, safer mutation routes, phase-supporting indexes, and regression coverage. Legacy destructive endpoints for the redesigned modules are retired or routed to a 410 response.

- All admin PDF exports in Phases 1-7 use Laravel Browsershot (`spatie/browsershot`) to render trusted server-side Blade export views through headless Chrome.
- PDF export endpoints preserve the active page filters and return an A4 PDF download; Print remains a separate browser print workflow.
- The deployment/runtime must provide Node.js, Puppeteer, and a Chrome/Chromium browser (`npm install puppeteer` and `npx puppeteer browsers install chrome`).
- Do not use client-side `html2pdf` for these management pages.

# Phase 1: Responsive School Class Management

## Summary

Redesign `/admin/class_list` using the approved responsive admin system while securing class and section changes against tenant leaks and destructive academic-data cascades.

## Key Changes

- Add summary cards for Total Classes, Total Sections, Classes With Students, and Empty Classes, independent of active filters.
- Add GET-based class/section search, usage filtering, reset behavior, and query-preserving pagination.
- Use a desktop table and mobile cards showing class name, section badges/count, student count, subject count, and actions.
- Combine class-name and section management into one responsive Create/Edit modal with dynamic section rows, inline validation, preserved input, and removal confirmation.
- Require every class to retain at least one uniquely named section.
- Add distinct empty-directory and no-matching-results states, safe wrapping, visible focus states, 44px controls, and breakpoints near 1200px, 768px, and 576px.
- Preserve Browsershot PDF and browser Print for the displayed filtered page with dedicated export/print views and styles.
- Move inline scripts/styles into dedicated Class Management JavaScript and scoped admin CSS.

## Backend and Interfaces

- Move class behavior into a typed `SchoolClassController` using route-model binding, policies, tenant-scoped queries, relationships, aggregate counts, and transactions.
- Replace unsafe routes with:
  - `POST /admin/classes` for creation.
  - `PATCH /admin/classes/{class}` for the atomic class-and-section update.
  - `DELETE /admin/classes/{class}` for deletion.
- Submit the class name, retained/new section rows, and explicit removed-section IDs; never encode deletion instructions inside section IDs.
- Add Store and Update Form Requests validating trimmed names, length limits, distinct sections, tenant-owned section IDs, and case-insensitive uniqueness.
- Permit active school admins with `admin.class_list` to view classes; only the primary school admin may create, edit, or delete them.
- Reject classes and sections outside the current school, and derive all `school_id` values from authentication.
- Allow an unchanged class name during editing while rejecting another class with the same normalized name.
- Update class and section changes inside one transaction; reject the complete update if any requested section removal is unsafe.
- Block section deletion when enrollments, attendance, routines, syllabuses, teacher permissions, gradebooks, or feedback reference it.
- Block class deletion when sections contain linked records or the class has subjects, exams, enrollments, attendance, routines, syllabuses, fees, book issues, teacher permissions, gradebooks, appraisals, or feedback. Permanently delete only genuinely unused classes.
- Remove manual cascade queries from the controller and return clear dependency-specific flash messages.
- Add separate tenant data and schema migrations that:
  - Safely rename later case-insensitive duplicate classes and sections using deterministic numeric suffixes.
  - Preserve all existing IDs and relationships.
  - Add unique indexes for `(school_id, name)` on classes and `(class_id, name)` on sections.
- Ensure legacy POST update and destructive GET delete routes return Method Not Allowed after migration to PATCH/DELETE.

## Tests and Acceptance Checks

- Test authentication, Class List permission, primary-only mutations, secondary-admin read-only access, and tenant isolation.
- Test class/section search, usage filters, independent totals, aggregate counts, pagination preservation, and both empty states.
- Test creation with one or multiple sections, duplicate names, whitespace/case variants, empty sections, and length limits.
- Test atomic class and section updates, unchanged names, additions, renames, removals, foreign section IDs, and rollback on any failure.
- Test blocking removal of the last section and sections with every supported dependency type.
- Test class deletion blocking across academic and financial dependencies and permanent deletion of an unused class.
- Test deterministic duplicate-name repair and both composite unique constraints.
- Verify escaped output, long names, many sections, desktop/mobile markup, keyboard interaction, PDF/print output, and no horizontal overflow.
- Compile Blade views, syntax-check JavaScript, and run Class Management tests plus Teacher Permission, Teacher Directory, Student, Attendance, Routine, Fee, and Gradebook regressions.

## Assumptions

- Class editing uses one combined class-and-section modal.
- Every class must have at least one section.
- Existing duplicate names are renamed rather than merged or deleted.
- Deletion remains permanent but is available only for unused classes and sections.
- No class archive/status field, bulk actions, subject redesign, enrollment reassignment workflow, or wider academic-module redesign is included.

# Phase 2: Responsive School Class Room Management

## Summary

Redesign `/admin/class_room` as a responsive, tenant-safe room directory that exposes routine usage and prevents deletion of rooms still referenced by the timetable.

## Key Changes

- Add independent summary cards for Total Rooms, In Use, Available, and Routine Slots.
- Add GET-based room-name search and usage filters with reset behavior and query-preserving ten-item pagination.
- Use a desktop table and mobile cards showing room name, routine count, usage status, and actions.
- Redesign Create and Edit modals with inline validation, visible focus states, 44px controls, and responsive layouts.
- Preserve Browsershot PDF and browser Print for the displayed filtered page and move all page behavior into dedicated JavaScript and scoped CSS.

## Backend and Interfaces

- Move room behavior into a typed `SchoolClassRoomController` using route-model binding, a `ClassRoomPolicy`, tenant-scoped queries, and a `routines` relationship with aggregate counts.
- Replace unsafe routes with `POST /admin/class-rooms`, `PATCH /admin/class-rooms/{classRoom}`, and `DELETE /admin/class-rooms/{classRoom}` while preserving `GET /admin/class_room`.
- Add Store and Update Form Requests that trim names, enforce length limits, and validate case-insensitive uniqueness within the authenticated school.
- Allow active school admins with `admin.class_room_list` to view rooms; only the primary school admin may create, edit, or delete them.
- Block deletion when any routine references the room and return a clear dependency message.
- Add separate tenant data and schema migrations to trim names, deterministically suffix later duplicates, preserve IDs, and add a unique `(school_id, name)` index.
- Ensure old POST-update and destructive GET-delete routes return Method Not Allowed.

## Tests and Acceptance Checks

- Test authentication, permission access, primary-only mutations, secondary-admin read-only access, tenant isolation, and foreign route models.
- Test search, usage filters, independent totals, pagination preservation, empty states, case/whitespace duplicates, and unchanged names.
- Test routine-linked deletion blocking, safe unused deletion, migration repair, the unique index, route methods, CSRF, redirects, and flash messages.
- Verify responsive markup, escaped long names, PDF/print output, keyboard behavior, no Blade queries, and no inline scripts/styles.

## Assumptions

- A room is considered In Use when at least one routine references it.
- Room deletion is permanent and allowed only for unused rooms.
- Capacity, building, floor, equipment, booking, and availability-calendar fields are outside this phase.

# Phase 3: Responsive School Subject Management

## Summary

Redesign `/admin/subject` as a responsive subject directory with session-aware uniqueness, secure class assignment, and protection for linked academic records.

## Key Changes

- Add independent summary cards for Total Subjects, Classes Covered, Scheduled Subjects, and Unused Subjects.
- Add GET-based subject search plus class, session, and usage filters with query-preserving pagination.
- Use a desktop table and mobile cards showing subject, class, session, routine usage, and dependency status.
- Redesign Create and Edit modals with dependent class/session controls, inline validation, accessible errors, and preserved input.
- Fix Academic navigation visibility so the `admin.subject_list` permission independently displays the group.

## Backend and Interfaces

- Move behavior into a typed `SchoolSubjectController` using route-model binding, a `SubjectPolicy`, eager-loaded relationships, aggregates, and tenant-scoped queries.
- Use `POST /admin/subjects` and `PATCH`/`DELETE /admin/subjects/{subject}` while preserving `GET /admin/subject`.
- Add tenant-scoped JSON endpoints for class sections and subjects instead of returning unscoped HTML options.
- Add Store and Update Form Requests validating trimmed names, school-owned classes/sessions, length limits, and case-insensitive uniqueness within `(school_id, class_id, session_id)`.
- Default creation to the running session while permitting another school-owned session to be selected explicitly.
- Permit renaming a referenced subject, but block class/session movement after exams, routines, syllabuses, or gradebook marks reference it.
- Block deletion for the same dependencies, including subject IDs stored as keys in Gradebook marks JSON.
- Add separate data and schema migrations to repair duplicates deterministically and add a unique `(school_id, class_id, session_id, name)` index.
- Retire legacy POST-update and destructive GET-delete routes with Method Not Allowed behavior.

## Tests and Acceptance Checks

- Test permissions, tenant isolation, school-owned class/session validation, search/filter behavior, totals, pagination, and empty states.
- Test one/multiple classes, unchanged names, normalized duplicates, cross-school IDs, running-session defaults, and referenced-subject move prevention.
- Test each dependency-specific deletion block, unused deletion, duplicate repair, composite uniqueness, route methods, CSRF, and rollback.
- Verify responsive markup, escaped values, accessible modals, no Blade queries, and external-only JavaScript/CSS.

## Assumptions

- Subject identity is scoped to school, class, and session.
- Existing IDs and relationships are preserved; duplicate subjects are renamed rather than merged.
- Exam, marks, and curriculum-content redesigns are outside this phase.

# Phase 4: Responsive School Syllabus Management

## Summary

Redesign `/admin/syllabus` as a responsive document directory with safe uploads, validated academic hierarchy, tenant isolation, and reliable file cleanup.

## Key Changes

- Add summary cards for Total Documents, Classes Covered, Subjects Covered, and uploads from the last 30 days.
- Add GET-based title search plus class, section, subject, and session filters with query-preserving pagination.
- Use a desktop table and mobile cards showing title, class, section, subject, session, file type, upload date, and actions.
- Redesign Create and Edit modals with dependent selectors, upload progress/error feedback, preserved input, and accessible file controls.
- Preserve document viewing/download, Browsershot PDF directory export, and browser Print for the displayed filtered page.

## Backend and Interfaces

- Move behavior into a typed `SchoolSyllabusController` using route-model binding, a `SyllabusPolicy`, relationships, eager loading, and tenant-scoped queries.
- Use `POST /admin/syllabuses` and `PATCH`/`DELETE /admin/syllabuses/{syllabus}` while preserving `GET /admin/syllabus`.
- Add Store and Update Form Requests validating title limits, school-owned session, class/section/subject hierarchy, and PDF/DOC/DOCX files up to 10 MB.
- Generate UUID filenames in the existing compatible public syllabus directory; never trust client filenames.
- Preserve the current file when no replacement is uploaded and remove replaced/deleted files only after successful database changes.
- Allow permitted active admins to view; only the primary school admin may upload, edit, replace, or delete documents.
- Add a compound index for common `(school_id, session_id, class_id, section_id, subject_id)` filters.
- Remove the legacy fragment route and retire POST updates and destructive GET deletes.

## Tests and Acceptance Checks

- Test permission access, tenant isolation, hierarchy validation, cross-school IDs, all filters, summary totals, pagination, and empty states.
- Test valid uploads, invalid MIME/extension combinations, oversized files, UUID names, optional replacement, failed-update preservation, and cleanup.
- Test route methods, CSRF, authorization, redirects, flash messages, foreign-tenant records, and safe deletion.
- Verify responsive markup, escaped titles, accessible file controls, external assets, and no database queries in Blade.

## Assumptions

- Existing public syllabus URLs remain compatible for teacher, student, parent, and API consumers.
- Multiple documents may exist for the same class, section, subject, and session.
- Version history, approval workflows, private object storage, and content authoring are outside this phase.

# Phase 5: Responsive School Routine Management

## Summary

Redesign `/admin/routine` as a responsive weekly timetable with strict section, teacher, and room conflict prevention.

## Key Changes

- Add summary cards for Total Periods, Scheduled Teachers, Rooms Used, and Days Covered.
- Add GET-based filters for class, section, teacher, room, day, and session with reset behavior.
- Replace seven duplicated day sections with one desktop timetable and mobile day accordions generated from a prepared weekly dataset.
- Redesign Create and Edit modals with dependent selectors, clear conflict feedback, validated time controls, and preserved input.
- Preserve filtered Browsershot PDF and browser Print output using dedicated export/print views and external JavaScript.

## Backend and Interfaces

- Move behavior into a typed `SchoolRoutineController` using route-model binding, a `RoutinePolicy`, eager-loaded relationships, and tenant-scoped queries.
- Use `POST /admin/routines` and `PATCH`/`DELETE /admin/routines/{routine}` while preserving `GET /admin/routine`.
- Add Form Requests validating school-owned class, section, subject, session, active role-3 teacher, room, allowed weekday, and start/end time.
- Normalize times to minutes and require the end to be later than the start.
- Reject overlapping slots in the same session/day for a matching section, teacher, or room. Allow adjacent periods and exclude the current record during updates.
- Save conflict-sensitive mutations inside transactions using row locks where concurrent saves could race.
- Repair orphan or cross-school room references to null, make `room_id` nullable, and add a foreign key with `nullOnDelete`; display missing rooms as Unassigned.
- Add compound indexes for section/day, teacher/day, and room/day conflict queries.
- Retire the fragment list route, POST updates, and destructive GET deletes.

## Tests and Acceptance Checks

- Test permissions, tenant isolation, every hierarchy rule, active-teacher validation, filtered weekly output, summaries, and empty states.
- Test section, teacher, and room conflicts; containment overlaps; equal slots; adjacent periods; different sessions/days; and self-excluding updates.
- Test invalid times, missing rooms, orphan repair, transaction rollback, route methods, CSRF, deletion, redirects, and flash messages.
- Verify responsive timetable/card markup, ordering from Saturday through Friday, PDF/print data, keyboard access, external assets, and no Blade queries.

## Assumptions

- Saturday through Friday remains the displayed school week.
- All section, teacher, and room overlaps are hard validation failures.
- A routine may remain readable with an Unassigned room after historical data repair, but new records require a valid room.
- Automatic timetable generation and substitute-teacher workflows are outside this phase.

# Phase 6: Responsive School Attendance Management

## Summary

Redesign `/admin/attendance` with responsive monthly reporting, secure daily roster entry, atomic updates, and streamed CSV export.

## Key Changes

- Add summary cards for Enrolled Students, Present Entries, Absent Entries, and Attendance Rate for the selected session and period.
- Add GET-based class, section, session, month, year, student search, and attendance-status filters.
- Use a sticky desktop month matrix and mobile student cards without page-level horizontal overflow.
- Redesign Take Attendance as an accessible date-and-roster interface with Present All, Absent All, per-student controls, loading/error feedback, and unsaved-change warning.
- Preserve CSV, Browsershot PDF, and browser Print output for the selected report.

## Backend and Interfaces

- Move behavior into a typed `SchoolAttendanceController` using an attendance policy, tenant-scoped enrollment queries, eager loading, conditional aggregates, and transactions.
- Use `GET /admin/attendance`, `GET /admin/attendance/entry`, `PUT /admin/attendance`, and `GET /admin/attendance/export.csv`.
- Add Filter and Update Form Requests validating a real non-future date, school-owned class/section/session, role-7 students, current enrollment, distinct IDs, and boolean states.
- Submit the complete displayed roster and save it atomically with `upsert()`; omitted students remain unchanged and any invalid row rolls back the submission.
- Remove all attendance queries from Blade and prepare the month matrix in the controller/service layer.
- Stream CSV directly with safe escaping and response headers; never create a temporary export file in the project root.
- Normalize duplicate attendance rows by retaining the newest record and add a unique `(school_id, session_id, class_id, section_id, student_id, timestamp)` index.
- Add supporting report indexes and retire legacy POST/fragments that duplicate the new interface.

## Tests and Acceptance Checks

- Test view/mutation permissions, tenant isolation, class/section/session ownership, enrollment validation, wrong-role students, and future dates.
- Test roster creation, correction, Present All, Absent All, omitted students, duplicate IDs, complete rollback, concurrent saves, and unique enforcement.
- Test monthly totals, attendance rate, all filters, empty states, query-preserving URLs, and duplicate-data repair.
- Test streamed CSV escaping/content/disposition and verify PDF/print output, responsive markup, keyboard operation, external assets, and no Blade queries.

## Assumptions

- Integer day timestamps remain for compatibility with teacher, student, parent, dashboard, and API consumers.
- The running session is selected by default, but another school-owned session may be viewed or corrected.
- Only the six admin pages are visually redesigned; other role-facing attendance screens remain compatible and unchanged.
- Leave requests, late status, remarks, notifications, and biometric integrations are outside this phase.

# Phase 7: Responsive School Gradebook Management

## Summary

Redesign `/admin/gradebook` as a responsive cohort report while securing its existing single-subject mark-entry flow and preserving the separate Marks module.

## Key Changes

- Add summary cards for Students, Results Recorded, Missing Results, and Cohort Average.
- Add GET-based student search plus class, section, exam category, subject, result-status, and session filters.
- Use a desktop report and mobile student cards showing subject marks, total, percentage where exam totals exist, and comment.
- Add distinct instructional, empty-cohort, and no-filter-result states with safe wrapping and accessible controls.
- Preserve Browsershot PDF and browser Print for the filtered cohort and warn before leaving with unsaved mark-entry changes.

## Backend and Interfaces

- Move behavior into a typed `SchoolGradebookController` using a `GradebookPolicy`, tenant-scoped relationships, eager loading, prepared mark maps, and aggregate queries.
- Preserve `GET /admin/gradebook` and replace the unsafe add-mark action with `POST /admin/gradebook/results`.
- Add Filter and Store Result Form Requests validating school-owned class, section, session, exam category, subject, current enrollment, role-7 student, numeric mark, and bounded comment.
- Require marks between zero and the matching offline exam’s total marks.
- Cast `marks` to an array and merge the submitted subject value into the student’s existing result row without replacing other subjects.
- Save each result scope transactionally and lock the existing row to prevent concurrent JSON overwrites.
- Normalize duplicate gradebooks by merging subject marks with the newest value winning and preserving the newest non-empty comment.
- Add a unique `(school_id, session_id, class_id, section_id, student_id, exam_category_id)` index and report-supporting indexes.
- Remove Gradebook fragment routes and database queries from Blade while leaving `/admin/marks` routes and behavior intact.

## Tests and Acceptance Checks

- Test permission access, primary-only entry, secondary-admin read-only behavior, tenant isolation, hierarchy validation, enrollment, and wrong-role users.
- Test every filter, independent summaries, pagination, missing-result detection, cohort averages, empty states, and escaped output.
- Test new rows, merging into existing marks, concurrent updates, mark bounds, comments, missing exam totals, rollback, duplicate normalization, and unique enforcement.
- Test route methods, CSRF, redirects, flash messages, responsive layouts, PDF/print output, unsaved-change behavior, external assets, and no Blade queries.
- Run Gradebook tests plus Marks, Teacher Permission, Teacher Directory, student/parent reporting, API, exam, enrollment, and full-suite regressions.

## Assumptions

- `/admin/marks` remains the full mark-management workflow and is not retired or merged into Gradebook.
- Gradebook keeps its existing JSON marks representation for cross-role compatibility.
- The newest duplicate value wins when historical rows disagree for the same subject.
- Grade publishing, transcripts, GPA rules, grading scales, moderation, and audit logs are outside this phase.
