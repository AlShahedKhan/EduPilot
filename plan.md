# Phase 1: Responsive School Accountant Directory

## Summary

Redesign `/admin/accountant` to match the approved Admin and Teacher directories, with responsive cards, accessible filtering, secure tenant-scoped CRUD, and protected financial history.

## Key Changes

- Add summary cards for All, Active, Disabled, and Recently Added accountants from the last 30 days.
- Add GET-based name/email search, status filtering, reset behavior, and query-preserving pagination.
- Use a structured desktop table and mobile cards showing identity, email, phone, address, joined date, and status.
- Add separate empty-directory and no-matching-results states, safe text wrapping, visible focus states, 44px controls, and responsive breakpoints near 1200px, 768px, and 576px.
- Redesign Create, Edit, and Profile modals with responsive fields, inline validation, safe profile defaults, password confirmation, and accessible photo cropping.
- Preserve Profile, Documents, PDF, and Print actions for the currently displayed filtered page.
- Move page behavior out of Blade into dedicated JavaScript and scoped Accountant Directory CSS.

## Backend and Interfaces

- Move accountant management into a typed `SchoolAccountantController` with route-model binding and tenant/role-scoped queries.
- Replace unsafe endpoints with:
  - `PATCH /admin/accountant/{accountant}` for updates.
  - `DELETE /admin/accountant/{accountant}` for deletion.
  - `PATCH /admin/accountant/{accountant}/status` for enable/disable.
  - `PATCH /admin/accountant/{accountant}/password` for password changes.
- Add dedicated GET/POST/DELETE accountant-document routes so document removal is no longer a destructive GET request.
- Add Form Requests for create, update, status, password, and document uploads.
- Validate normalized unique email, confirmed strong passwords, birthday, supported gender/blood group, phone/address limits, safe document types, and JPEG/PNG/WebP photos up to 2 MB.
- Use validated input only, UUID filenames, fixed upload directories, safe cropped-image references, and cleanup of replaced/deleted files.
- Allow active school admins with `admin.accountant` permission to view the directory and profiles; only the primary school admin may mutate accounts or documents.
- Reject targets outside the current school or with a role other than Accountant.
- Preserve successful creation if welcome-email delivery fails and report the mail failure separately.
- Block permanent deletion when payments, chats, or message threads reference the accountant; recommend disabling instead. Delete unlinked accountants permanently with confirmation and cleanup.
- Prevent legacy generic GET status and document endpoints from modifying accountant records.

## Tests and Acceptance Checks

- Test authentication, Accountant List permission, primary-only mutations, secondary-admin read-only access, and tenant/role isolation.
- Test search, Active/Disabled/Recent filters, independent totals, 30-day boundary behavior, pagination query preservation, and both empty states.
- Test create/update validation, duplicate emails, password hashing, invalid photos/documents, malformed profile JSON, generated filenames, and welcome-mail failure handling.
- Test PATCH/DELETE methods, CSRF forms, status transitions, linked-record deletion blocking, safe unlinked deletion, document authorization, redirects, and flash messages.
- Verify escaped output, missing profile fields, long values, desktop/mobile markup, PDF/print controls, keyboard interaction, and no inline scripts/styles.
- Compile Blade views, syntax-check JavaScript, and run Accountant Directory tests plus Contact Inbox, Admin Directory, and Teacher Directory regressions.

## Assumptions

- Mobile uses cards while tablet and desktop retain the table.
- Only the primary school admin may mutate accountant accounts.
- Deletion is allowed only for accountants without linked payment or message history.
- The fourth summary card represents accounts created in the last 30 days.
- No database migration, soft deletion, bulk actions, or accounting-workflow redesign is included.

# Phase 2: Responsive School Librarian Directory

## Summary

Redesign `/admin/librarian` with the same responsive, accessible, and tenant-safe system used by the Admin and Teacher directories.

## Key Changes

- Add summary cards for All, Active, Disabled, and Recently Added librarians from the last 30 days.
- Add GET-based name/email search, status filtering, reset behavior, and query-preserving pagination.
- Use a desktop table and mobile cards showing identity, email, phone, address, joined date, and account status.
- Add distinct empty-directory and no-matching-results states, safe wrapping, visible focus states, 44px controls, and breakpoints near 1200px, 768px, and 576px.
- Redesign Create, Edit, and Profile modals with responsive fields, inline validation, password confirmation, safe profile defaults, and accessible photo cropping.
- Preserve Profile, Documents, PDF, and Print actions for the displayed filtered page.
- Move inline scripts and styles into dedicated Librarian Directory JavaScript and scoped admin CSS.

## Backend and Interfaces

- Move librarian management into a typed `SchoolLibrarianController` with route-model binding and tenant/role-scoped queries.
- Replace unsafe routes with:
  - `PATCH /admin/librarian/{librarian}` for updates.
  - `DELETE /admin/librarian/{librarian}` for deletion.
  - `PATCH /admin/librarian/{librarian}/status` for enable/disable.
  - `PATCH /admin/librarian/{librarian}/password` for password changes.
- Add dedicated GET/POST/DELETE librarian-document routes, replacing destructive document-removal GET requests.
- Add Form Requests for create, update, status, password, and document uploads.
- Validate normalized unique email, confirmed strong passwords, birthday, gender, blood group, phone/address limits, safe documents, and JPEG/PNG/WebP photos up to 2 MB.
- Use validated input, UUID filenames, fixed upload directories, safe cropped-image references, and cleanup of replaced/deleted files.
- Allow active school admins with `admin.librarian` permission to view librarians; only the primary school admin may mutate accounts or documents.
- Reject users outside the current school or whose role is not Librarian.
- Preserve librarian creation when welcome-email delivery fails and report the mail problem separately.
- Block deletion when payments, chats, or message threads reference the librarian; recommend disabling the account instead. Permanently delete unlinked librarians after confirmation.
- Prevent legacy generic GET status and document endpoints from modifying librarian accounts.

## Tests and Acceptance Checks

- Test authentication, Librarian List permission, primary-only mutations, secondary-admin read-only access, tenant isolation, and role isolation.
- Test search, Active/Disabled/Recent filters, independent totals, the 30-day boundary, pagination query preservation, and both empty states.
- Test create/update validation, duplicate emails, password hashing, malformed profile JSON, invalid files, safe filenames, and welcome-mail failure handling.
- Test PATCH/DELETE routes, CSRF forms, status updates, linked-record deletion blocking, unlinked deletion, document authorization, redirects, and flash messages.
- Verify escaped output, missing profile values, long content, responsive markup, PDF/print controls, keyboard interaction, and absence of inline scripts/styles.
- Compile Blade views, syntax-check JavaScript, and run Librarian Directory tests plus Contact Inbox, Admin Directory, Teacher Directory, and Accountant Directory regressions.

## Assumptions

- Mobile uses cards while tablet and desktop retain the table.
- Only the primary school admin may mutate librarian accounts.
- The fourth summary card represents librarians created within the last 30 days.
- Deletion is allowed only when no payment or message history references the librarian.
- Book, book-issue, and wider library-workflow screens are outside this phase.
- No database migration, soft deletion, or bulk actions are included.

# Phase 3: Responsive School Parent Directory

## Summary

Redesign `/admin/parent` using the approved responsive directory system while securing parent-student assignments and preserving linked academic and financial history.

## Key Changes

- Add independent summary cards for All, Active, Disabled, and Without Children.
- Add GET-based name/email/child-name search, account-status filtering, child-link filtering, reset behavior, and query-preserving pagination.
- Use a desktop table and mobile cards showing parent identity, contact details, linked-child count/names, account status, and actions.
- Add distinct empty-directory and no-matching-results states, safe text wrapping, visible focus states, 44px controls, and breakpoints near 1200px, 768px, and 576px.
- Redesign Create, Edit, and Profile screens with responsive fields, inline validation, password confirmation, safe profile defaults, and accessible photo cropping.
- Replace the fragile child rows with an accessible class to section to student selector, removable child chips, duplicate prevention, loading/error feedback, and preserved selections after validation errors.
- Show linked children with class and section information on the profile page.
- Preserve Profile, Documents, PDF, and Print actions for the displayed filtered page.
- Move inline scripts/styles into dedicated Parent Directory JavaScript and scoped admin CSS.

## Backend and Interfaces

- Move parent management into a typed `SchoolParentController` using route-model binding, policies, tenant-scoped queries, eager loading, and `withCount()` for children.
- Add explicit `children` and `parent` relationships to `User` and normalize malformed or missing profile JSON before rendering.
- Replace unsafe routes with:
  - `PATCH /admin/parent/{parent}` for updates.
  - `DELETE /admin/parent/{parent}` for deletion.
  - `PATCH /admin/parent/{parent}/status` for enable/disable.
  - `PATCH /admin/parent/{parent}/password` for password changes.
- Add dedicated GET/POST/DELETE parent-document routes, replacing destructive document-removal GET requests.
- Add tenant-scoped JSON endpoints for class sections and eligible students; return only role-7 students enrolled in the selected school/class/section.
- Add Form Requests for create, update, status, password, child assignment, and document uploads.
- Validate normalized unique email, confirmed strong passwords, optional distinct child IDs, birthday, gender, blood group, phone/address limits, safe documents, and JPEG/PNG/WebP photos up to 2 MB.
- Create/update the parent and synchronize child links inside a database transaction. Lock selected student rows and recheck ownership to prevent concurrent reassignment.
- Allow assigning only unassigned students or students already linked to the parent being edited. Block students belonging to another parent with clear validation feedback.
- Use validated input, UUID filenames, fixed upload directories, safe cropped-image references, and cleanup of replaced/deleted files.
- Allow active school admins with `admin.parent` permission to view parents and profiles; only the primary school admin may create, edit, disable, delete, change passwords, manage children, or manage documents.
- Reject parent or student targets outside the current school and users with incorrect roles.
- Preserve successful parent creation if welcome-email delivery fails and report the mail failure separately.
- Block permanent deletion when the parent has linked children, fee-manager records, feedback, payments, chats, or message threads; recommend disabling instead. Permanently delete only fully unlinked parents after confirmation and file cleanup.
- Prevent legacy generic GET status and document endpoints from modifying parent accounts.

## Tests and Acceptance Checks

- Test authentication, Parent List permission, primary-only mutations, secondary-admin read-only access, tenant isolation, and role isolation.
- Test search, Active/Disabled/Without Children filters, independent totals, child counts, pagination query preservation, and both empty states.
- Test creation and child synchronization with no children, one child, multiple children, duplicate IDs, wrong-school students, invalid enrollments, and students already assigned elsewhere.
- Verify updates detach removed children only after successful validation and roll back all parent/child changes on failure.
- Test duplicate emails, password hashing, invalid photos/documents, malformed profile JSON, generated filenames, and welcome-mail failure handling.
- Test PATCH/DELETE methods, CSRF forms, status changes, linked-record deletion blocking, safe unlinked deletion, document authorization, redirects, and flash messages.
- Verify escaped output, missing profile fields, long parent/child names, responsive markup, PDF/print controls, keyboard interaction, and absence of inline scripts/styles.
- Compile Blade views, syntax-check JavaScript, and run Parent Directory tests plus Contact Inbox, Admin Directory, Teacher Directory, Accountant Directory, and Librarian Directory regressions.

## Assumptions

- Mobile uses cards while tablet and desktop retain the table.
- Parents may exist without linked children; class and section fields are selection helpers rather than stored parent attributes.
- Students already assigned to another parent cannot be silently reassigned.
- Only the primary school admin may mutate parent accounts or family links.
- Disabling a parent never removes child or historical-record links.
- No database migration, soft deletion, bulk actions, or redesign of student, fee, feedback, or messaging workflows is included.

# Phase 4: Responsive School Warden Directory

## Summary

Redesign `/admin/warden` using the approved responsive directory system. Secure tenant-scoped account management, expose hostel assignments clearly, protect operational history, and repair the missing role-10 Warden seed definition.

## Key Changes

- Add independent summary cards for All, Active, Disabled, and Unassigned wardens.
- Add GET-based name/email/hostel search, status and assignment filters, reset behavior, and query-preserving pagination.
- Use a desktop table and mobile cards showing identity, email, phone, assigned hostels, joined date, status, and actions.
- Show assigned-hostel counts and names without queries inside Blade.
- Add distinct empty-directory and no-matching-results states, safe wrapping, visible focus states, 44px controls, and responsive breakpoints near 1200px, 768px, and 576px.
- Redesign Create, Edit, and Profile modals with responsive fields, inline validation, password confirmation, safe profile defaults, accessible labels, and photo cropping.
- Show assigned hostels in the profile as read-only information; continue managing assignments from the existing Hostel screen.
- Preserve Profile, Documents, PDF, and Print for the displayed filtered page.
- Move inline scripts/styles into dedicated Warden Directory JavaScript and scoped admin CSS.

## Backend and Interfaces

- Move directory behavior into a typed `SchoolWardenController` using route-model binding, policies, tenant-scoped queries, and eager-loaded `hostels`.
- Add the `User::hostels()` and `Hostel::warden()` relationships and normalize malformed profile JSON before rendering.
- Replace unsafe routes with:
  - `PATCH /admin/warden/{warden}` for updates.
  - `DELETE /admin/warden/{warden}` for deletion.
  - `PATCH /admin/warden/{warden}/status` for enable/disable.
  - `PATCH /admin/warden/{warden}/password` for password changes.
- Add dedicated GET/POST/DELETE Warden document routes, replacing destructive document-removal GET requests.
- Add Form Requests for create, update, status, password, and document uploads.
- Validate normalized unique email, confirmed strong passwords, birthday, supported gender/blood group, phone/address limits, safe documents, and JPEG/PNG/WebP photos up to 2 MB.
- Use validated input only, role `10`, current school ID, enabled status for new accounts, UUID filenames, safe cropped-image references, and cleanup of replaced/deleted files.
- Allow active school admins with `admin.warden` permission to view wardens; only the primary school admin may create, edit, disable, delete, change passwords, or manage documents.
- Reject targets outside the current school or whose role is not Warden.
- Preserve successful creation if welcome-email delivery fails and report/log the mail failure separately.
- Block deletion when hostels, payments, chats, or message threads reference the warden; direct the administrator to reassign hostels or disable the account. Permanently delete only unlinked wardens after confirmation and file cleanup.
- Allow emergency disabling of an assigned warden, retain hostel assignments, and show a warning that those hostels require reassignment.
- Prevent legacy generic GET status and document endpoints from modifying Warden accounts.
- Add an idempotent tenant data migration inserting role `10` as `warden` when absent, and update `InitialDataSeeder` so new tenants receive it automatically.

## Tests and Acceptance Checks

- Test authentication, Warden List permission, primary-only mutations, secondary-admin read-only access, tenant isolation, and role isolation.
- Test name/email/hostel search, Active/Disabled/Unassigned filters, independent totals, multiple-hostel display, and query-preserving pagination.
- Test empty data, filtered empty results, missing assignments, malformed profile JSON, missing contact details, and long values.
- Test create/update validation, duplicate emails, password hashing, account defaults, invalid files, generated filenames, and welcome-mail failure handling.
- Test PATCH/DELETE methods, CSRF forms, status transitions, assigned-hostel deletion blocking, historical-record blocking, safe unlinked deletion, redirects, and flash messages.
- Verify disabling an assigned warden preserves hostel links and returns the reassignment warning.
- Test the role-10 migration for both missing and already-existing role records.
- Verify responsive markup, keyboard interaction, focus visibility, PDF/print output, escaped content, and absence of inline scripts/styles.
- Compile Blade views, syntax-check JavaScript, and run Warden Directory tests plus the previous directory and Contact Inbox regression tests.

## Assumptions

- Mobile uses cards while tablet and desktop retain the table.
- The fourth summary card represents wardens without an assigned hostel.
- Hostel assignment remains controlled by the Hostel management screen because `warden_id` belongs to each hostel.
- A warden may manage multiple hostels.
- Only the primary school admin may mutate Warden accounts.
- One idempotent data migration is included to repair role metadata; no schema changes, soft deletion, bulk actions, or hostel-workflow redesign is included.

# Phase 5: Responsive Teacher Permission Management

## Summary

Redesign `/admin/permission` as a responsive, tenant-safe permission matrix for assigning Class Access, Marks, and Attendance permissions to teachers.

## Key Changes

- Use GET-based class, section, teacher search, account-status, and permission filters with query-preserving pagination.
- Add summary cards for Teachers, Class Access, Marks Access, and Attendance Access, independent of teacher filters.
- Display a structured desktop permission table and touch-friendly teacher cards below 768px.
- Show disabled teachers as read-only with a clear Disabled label.
- Add an explicit Class Access switch:
  - Enabling it creates the class/section permission.
  - Disabling it revokes Marks and Attendance and removes the permission row.
- Use a Save Changes button to submit only teachers shown on the current page in one transaction.
- Warn before leaving the page when changes are unsaved.
- Add instructional, empty-teacher, and no-filter-results states.
- Use accessible switch labels, keyboard controls, visible focus states, safe text wrapping, and 44px mobile targets.
- Move inline JavaScript and styles into dedicated assets with scoped permission-page CSS.

## Backend and Interfaces

- Move behavior into a typed `SchoolTeacherPermissionController`.
- Replace the AJAX fragment and state-changing GET endpoint with:
  - `GET /admin/permission` for filters and the permission matrix.
  - `GET /admin/permission/classes/{class}/sections` for tenant-scoped section options.
  - `PUT /admin/permission` for transactional page-level updates.
- Submit `class_id`, `section_id`, and a teacher-keyed permission array containing `class_access`, `marks`, and `attendance`.
- Add Form Requests for filter input and permission updates.
- Validate that:
  - The class belongs to the current school.
  - The section belongs to that class and school.
  - Every submitted user is a role-3 teacher from the same school.
  - Permission values are booleans.
  - Disabled teachers are not modified.
- Permit active school admins with `admin.teacher.permission` to view the page; only the primary school admin may modify permissions.
- Fix the sidebar permission-key mismatch so it consistently uses `admin.teacher.permission`.
- Add typed relationships and boolean casts to `TeacherPermission`, then eager-load permission rows instead of querying inside Blade.
- Use a transaction and `upsert()` for granted permissions; delete a teacher's row when Class Access is revoked.
- Add a tenant migration that:
  - Normalizes nullable Marks and Attendance values to zero.
  - Merges duplicate `(school_id, class_id, section_id, teacher_id)` rows by preserving any granted permission.
  - Adds a composite unique index preventing future duplicates.
- Update teacher-side authorization:
  - General class and syllabus access requires an existing permission row.
  - Marks operations require `marks = 1`.
  - Attendance operations require `attendance = 1`.
  - Enforce permissions on write endpoints, not only dropdown visibility.
- Remove the legacy permission-list fragment route and ensure the old permission-update GET returns Method Not Allowed.

## Tests and Acceptance Checks

- Test authentication, view permission, primary-only updates, secondary-admin read-only access, and tenant isolation.
- Test class/section ownership, mismatched sections, wrong-role users, cross-school teachers, invalid booleans, and disabled-teacher updates.
- Test search, status and permission filters, independent totals, pagination preservation, and all empty states.
- Test page-level updates, partial-page saving, permission creation, Marks/Attendance changes, and complete revocation.
- Verify failed validation rolls back the entire submitted update.
- Test duplicate normalization and the composite unique constraint.
- Confirm teacher Marks, Attendance, and syllabus endpoints enforce their corresponding permissions.
- Verify responsive markup around 1440px, 1024px, 768px, 390px, and 320px.
- Confirm keyboard operation, focus visibility, unsaved-change warning, escaped output, and no horizontal overflow.
- Compile Blade views, syntax-check JavaScript, and run permission tests plus Teacher Directory and related regression tests.

## Assumptions

- The existing permission row represents general Class Access; no new permission column is required.
- Disabled teachers remain visible but cannot be edited.
- Saving updates only teachers included on the current paginated page.
- No bulk "grant everyone" action, audit-log system, role redesign, or unrelated Teacher portal redesign is included.
