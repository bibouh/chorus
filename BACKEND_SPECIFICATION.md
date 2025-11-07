# Choir Management System - Backend Specification

## Project Overview

This is a Laravel backend API for a Choir (Chorale) Management System. The system manages choir members, events, attendance tracking via QR codes, and provides comprehensive reporting features.

## Database Schema

### 1. Users Table

**Purpose**: Authentication and authorization for system administrators and managers.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | string(255) | NOT NULL | Full name of the user |
| email | string(255) | UNIQUE, NOT NULL | Email address (used for login) |
| email_verified_at | timestamp | NULLABLE | Email verification timestamp |
| password | string(255) | NOT NULL | Hashed password |
| role | enum | NOT NULL, DEFAULT 'manager' | User role: 'admin', 'manager' |
| remember_token | string(100) | NULLABLE | Remember me token |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Has many Events (created_by)
- Has many Attendances (recorded_by)

---

### 2. Members Table

**Purpose**: Stores information about choir members (chorists).

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| member_code | string(50) | UNIQUE, NOT NULL | Unique member code (e.g., CHORALE001) |
| qr_code | string(100) | UNIQUE, NOT NULL | QR code identifier for scanning |
| name | string(255) | NOT NULL | Full name of the member |
| email | string(255) | UNIQUE, NOT NULL | Email address |
| phone | string(50) | NOT NULL | Phone number |
| address | text | NULLABLE | Full address |
| voice_part | enum | NOT NULL | Voice part: 'soprano', 'alto', 'tenor', 'bass', 'contralto', 'mezzo_soprano', 'baritone', 'bass_profundo' |
| join_date | date | NOT NULL | Date when member joined the choir |
| is_active | boolean | NOT NULL, DEFAULT true | Whether the member is currently active |
| notes | text | NULLABLE | Additional notes about the member |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Has many Attendances
- Has many QRCodeDistributions

**Indexes**:
- Index on `member_code`
- Index on `qr_code`
- Index on `email`
- Index on `voice_part`
- Index on `is_active`

---

### 3. Event Types Table

**Purpose**: Stores predefined event types for categorization.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | string(100) | UNIQUE, NOT NULL | Event type name (e.g., 'Rehearsal', 'Concert') |
| slug | string(100) | UNIQUE, NOT NULL | URL-friendly identifier |
| description | text | NULLABLE | Description of the event type |
| color | string(7) | NULLABLE | Hex color code for UI display |
| icon | string(50) | NULLABLE | Icon identifier |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Default Event Types**:
- Rehearsal (Répétition)
- Concert
- General Rehearsal (Répétition générale)
- Technical Rehearsal (Répétition technique)
- Rehearsal with Soloists (Répétition avec solistes)
- Orchestra Rehearsal (Répétition d'orchestre)
- Gala Concert (Concert de gala)
- Charity Concert (Concert de bienfaisance)
- Other (Autre)

---

### 4. Events Table

**Purpose**: Stores choir events (rehearsals, concerts, etc.).

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | string(255) | NOT NULL | Event name |
| description | text | NULLABLE | Event description |
| event_type_id | bigint | FOREIGN KEY, NOT NULL | Reference to event_types table |
| date | date | NOT NULL | Event date |
| time | time | NOT NULL | Event start time |
| is_recurring | boolean | NOT NULL, DEFAULT false | Whether this is a recurring event |
| parent_event_id | bigint | FOREIGN KEY, NULLABLE | Reference to parent recurring event |
| created_by | bigint | FOREIGN KEY, NOT NULL | User who created the event |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Belongs to EventType
- Belongs to User (created_by)
- Has many Attendances
- Has many RecurringEventSchedules (if recurring)
- Belongs to Event (parent_event_id) - for recurring events

**Indexes**:
- Index on `event_type_id`
- Index on `date`
- Index on `is_recurring`
- Index on `parent_event_id`

---

### 5. Recurring Event Schedules Table

**Purpose**: Stores schedule information for recurring events.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| event_id | bigint | FOREIGN KEY, NOT NULL | Reference to events table |
| day_of_week | tinyint | NOT NULL | Day of week (0=Sunday, 1=Monday, ..., 6=Saturday) |
| end_date | date | NOT NULL | Last occurrence date |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Belongs to Event

**Indexes**:
- Index on `event_id`
- Index on `day_of_week`
- Composite index on `(event_id, day_of_week)`

---

### 6. Attendances Table

**Purpose**: Tracks member attendance at events.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| event_id | bigint | FOREIGN KEY, NOT NULL | Reference to events table |
| member_id | bigint | FOREIGN KEY, NOT NULL | Reference to members table |
| status | enum | NOT NULL | Attendance status: 'present', 'late', 'absent' |
| arrival_time | time | NULLABLE | Time when member arrived (if present/late) |
| scanned_at | timestamp | NULLABLE | When the QR code was scanned |
| recorded_by | bigint | FOREIGN KEY, NULLABLE | User who recorded the attendance |
| notes | text | NULLABLE | Additional notes |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Belongs to Event
- Belongs to Member
- Belongs to User (recorded_by)

**Indexes**:
- Index on `event_id`
- Index on `member_id`
- Index on `status`
- Composite unique index on `(event_id, member_id)` - One attendance record per member per event
- Index on `scanned_at`

---

### 7. Late Detection Settings Table

**Purpose**: Stores configuration for automatic late detection.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| is_enabled | boolean | NOT NULL, DEFAULT true | Whether late detection is enabled |
| default_threshold_minutes | integer | NOT NULL, DEFAULT 15 | Default late threshold in minutes |
| auto_mark_late | boolean | NOT NULL, DEFAULT true | Automatically mark as late if threshold exceeded |
| send_notifications | boolean | NOT NULL, DEFAULT true | Send notifications when member is late |
| use_different_thresholds_by_type | boolean | NOT NULL, DEFAULT false | Use different thresholds per event type |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Note**: This table should typically have only one row (singleton pattern).

---

### 8. Event Type Late Thresholds Table

**Purpose**: Stores late detection thresholds per event type (if enabled).

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| event_type_id | bigint | FOREIGN KEY, NOT NULL | Reference to event_types table |
| threshold_minutes | integer | NOT NULL | Late threshold in minutes for this event type |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Belongs to EventType

**Indexes**:
- Unique index on `event_type_id`

---

### 9. QR Code Distributions Table

**Purpose**: Tracks QR code distribution to members.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| member_id | bigint | FOREIGN KEY, NOT NULL | Reference to members table |
| distribution_method | enum | NOT NULL | Method: 'email', 'sms', 'whatsapp', 'print', 'direct_share' |
| sent_at | timestamp | NULLABLE | When the QR code was sent |
| sent_by | bigint | FOREIGN KEY, NULLABLE | User who sent the QR code |
| include_instructions | boolean | NOT NULL, DEFAULT true | Whether instructions were included |
| include_qr_image | boolean | NOT NULL, DEFAULT true | Whether QR image was included |
| include_member_info | boolean | NOT NULL, DEFAULT true | Whether member info was included |
| status | enum | NOT NULL, DEFAULT 'pending' | Status: 'pending', 'sent', 'failed' |
| error_message | text | NULLABLE | Error message if distribution failed |
| created_at | timestamp | NULLABLE | Creation timestamp |
| updated_at | timestamp | NULLABLE | Update timestamp |

**Relationships**:
- Belongs to Member
- Belongs to User (sent_by)

**Indexes**:
- Index on `member_id`
- Index on `distribution_method`
- Index on `status`
- Index on `sent_at`

---

### 10. QR Code Generation History Table

**Purpose**: Tracks when new QR codes are generated for members.

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| member_id | bigint | FOREIGN KEY, NOT NULL | Reference to members table |
| old_qr_code | string(100) | NULLABLE | Previous QR code (before regeneration) |
| new_qr_code | string(100) | NOT NULL | New QR code |
| generated_by | bigint | FOREIGN KEY, NOT NULL | User who generated the new code |
| reason | text | NULLABLE | Reason for regeneration |
| created_at | timestamp | NULLABLE | Creation timestamp |

**Relationships**:
- Belongs to Member
- Belongs to User (generated_by)

**Indexes**:
- Index on `member_id`
- Index on `created_at`

---

## Relationships Summary

```
Users
├── hasMany Events (created_by)
└── hasMany Attendances (recorded_by)

Members
├── hasMany Attendances
├── hasMany QRCodeDistributions
└── hasMany QRCodeGenerationHistory

EventTypes
├── hasMany Events
└── hasOne EventTypeLateThreshold

Events
├── belongsTo EventType
├── belongsTo User (created_by)
├── belongsTo Event (parent_event_id) - for recurring events
├── hasMany Attendances
└── hasMany RecurringEventSchedules

RecurringEventSchedules
└── belongsTo Event

Attendances
├── belongsTo Event
├── belongsTo Member
└── belongsTo User (recorded_by)

LateDetectionSettings
└── (Singleton - typically one row)

EventTypeLateThresholds
└── belongsTo EventType

QRCodeDistributions
├── belongsTo Member
└── belongsTo User (sent_by)

QRCodeGenerationHistory
├── belongsTo Member
└── belongsTo User (generated_by)
```

## API Endpoints Structure

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh token

### Members
- `GET /api/members` - List all members (with filters)
- `GET /api/members/{id}` - Get member details
- `POST /api/members` - Create new member
- `PUT /api/members/{id}` - Update member
- `DELETE /api/members/{id}` - Delete member
- `GET /api/members/{id}/attendance` - Get member attendance history
- `POST /api/members/{id}/generate-qr` - Generate new QR code
- `POST /api/members/bulk-generate-qr` - Generate QR codes for multiple members

### Events
- `GET /api/events` - List all events (with filters)
- `GET /api/events/{id}` - Get event details
- `POST /api/events` - Create new event
- `PUT /api/events/{id}` - Update event
- `DELETE /api/events/{id}` - Delete event
- `POST /api/events/{id}/generate-recurring` - Generate recurring event instances
- `GET /api/events/{id}/attendance` - Get event attendance list

### Attendances
- `GET /api/attendances` - List attendances (with filters)
- `POST /api/attendances/scan` - Record attendance via QR scan
- `PUT /api/attendances/{id}` - Update attendance record
- `DELETE /api/attendances/{id}` - Delete attendance record
- `GET /api/attendances/reports/by-member` - Attendance reports by member
- `GET /api/attendances/reports/by-voice-part` - Attendance reports by voice part
- `GET /api/attendances/reports/by-event` - Attendance reports by event

### Statistics
- `GET /api/statistics/events` - Event statistics
- `GET /api/statistics/attendance` - Attendance statistics
- `GET /api/statistics/members` - Member statistics

### Settings
- `GET /api/settings/late-detection` - Get late detection settings
- `PUT /api/settings/late-detection` - Update late detection settings
- `GET /api/settings/event-type-thresholds` - Get event type thresholds
- `PUT /api/settings/event-type-thresholds/{id}` - Update event type threshold

### QR Code Distribution
- `GET /api/qr-distributions` - List QR code distributions
- `POST /api/qr-distributions/send` - Send QR codes to members
- `GET /api/qr-distributions/{id}` - Get distribution details

## Business Logic Requirements

### Automatic Late Detection

When a member's QR code is scanned:
1. Check if late detection is enabled in `late_detection_settings`
2. Get the event's start time and date
3. Calculate the difference between current time and event start time
4. Determine the threshold:
   - If `use_different_thresholds_by_type` is true, get threshold from `event_type_late_thresholds` for the event's type
   - Otherwise, use `default_threshold_minutes` from `late_detection_settings`
5. If difference exceeds threshold:
   - Set status to 'late' if `auto_mark_late` is true
   - Send notification if `send_notifications` is true
6. Otherwise, set status to 'present'

### Recurring Events

When creating a recurring event:
1. Create the parent event in `events` table with `is_recurring = true`
2. Create entries in `recurring_event_schedules` for each selected day of week
3. Generate individual event instances up to the `end_date`
4. Each generated instance should have `parent_event_id` pointing to the parent event

### QR Code Generation

- QR codes should be unique and follow format: `CHORALE{timestamp}` or similar
- When regenerating:
  1. Store old QR code in `qr_code_generation_history`
  2. Generate new unique QR code
  3. Update member's `qr_code` field
  4. Invalidate old QR code (cannot be used for new attendances)

## Validation Rules

### Members
- `email`: Must be valid email format, unique
- `phone`: Must match phone number format (10+ digits)
- `member_code`: Must be unique
- `qr_code`: Must be unique
- `voice_part`: Must be one of the predefined enum values
- `join_date`: Cannot be in the future

### Events
- `name`: Required, min 3 characters
- `date`: Required, cannot be in the past (for new events)
- `time`: Required
- `event_type_id`: Must exist in event_types table
- For recurring events: `end_date` must be after `date`, at least one day of week must be selected

### Attendances
- `event_id` and `member_id`: Must exist
- Unique constraint: One attendance record per member per event
- `status`: Must be 'present', 'late', or 'absent'
- `arrival_time`: Required if status is 'present' or 'late'

## Additional Notes

1. **Soft Deletes**: Consider implementing soft deletes for Members and Events to maintain historical data
2. **Audit Trail**: Consider adding audit logs for sensitive operations (member updates, attendance changes)
3. **Notifications**: Implement queue system for sending QR codes and late notifications
4. **Caching**: Cache frequently accessed data like event types and late detection settings
5. **API Rate Limiting**: Implement rate limiting for QR scan endpoint to prevent abuse
6. **File Storage**: If storing QR code images, use Laravel's storage system

## Migration Priority

1. Users
2. EventTypes (seed with default types)
3. Members
4. Events
5. RecurringEventSchedules
6. Attendances
7. LateDetectionSettings (seed with default settings)
8. EventTypeLateThresholds
9. QRCodeDistributions
10. QRCodeGenerationHistory

