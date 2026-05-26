# Campus Service Request System — Worksheet Solution

---

## PART A — Analysis

### A1. Requirements List

- Students can submit a new service request (title, description, location).
- Students can view a list of their own requests.
- Students can view the details of a specific request.
- Staff can view all submitted requests.
- Staff can update the status of a request.
- Request status follows the flow: **Pending → In Progress → Done**.
- All form inputs must be validated (title cannot be empty).
- User-supplied data must be sanitized (prevent XSS).
- The system must redirect after form submission (Post/Redirect/Get).
- Unauthorized users cannot update request status.

---

### A2. User Roles

| Role    | Description                        | Permissions                                      |
|---------|------------------------------------|--------------------------------------------------|
| Student | Campus member who submits requests | Submit, view own requests, view details          |
| Staff   | Campus employee who resolves issues| View all requests, update status, filter by status |

---

### A3. Use Cases

#### Use Case 1 — Submit a Request
- **Actor:** Student
- **Trigger:** Student clicks "New Request"
- **Main Flow:**
  1. Student opens the create form
  2. Student fills in title, description, location
  3. Student clicks Submit
  4. System validates input
  5. System saves request with status = Pending
  6. System redirects to request list
- **Alternative Flow:** Step 4 fails → redisplay form with error messages
- **Success Outcome:** New request saved; student sees it in their list

#### Use Case 2 — View Request List
- **Actor:** Student / Staff
- **Trigger:** User navigates to `/requests`
- **Main Flow:**
  1. System loads all requests belonging to the user
  2. System renders a table (title, status, date)
- **Alternative Flow:** No requests found → show empty-state message
- **Success Outcome:** User sees a list of requests

#### Use Case 3 — View Request Details
- **Actor:** Student / Staff
- **Trigger:** User clicks a row in the request list
- **Main Flow:**
  1. System loads request by ID
  2. System renders title, description, location, status
- **Alternative Flow:** ID not found → 404 page
- **Success Outcome:** User sees full details of the request

#### Use Case 4 — Update Request Status
- **Actor:** Staff
- **Trigger:** Staff clicks "Update Status" on the detail page
- **Main Flow:**
  1. Staff selects a new status
  2. Staff clicks Save
  3. System validates the transition is allowed
  4. System saves new status
  5. System redirects back to detail page
- **Alternative Flow:** Invalid transition → show error "Status cannot go back"
- **Success Outcome:** Request status updated; detail page shows new status

---

## PART B — Design

### B4. Class Responsibility Table (SRP)

| Class Name          | Responsibility (1 sentence)                                  | Reason to Change          |
|---------------------|--------------------------------------------------------------|---------------------------|
| Request             | Stores request data: title, description, location, status   | Data fields change        |
| RequestRepository   | Reads/writes Request records to/from storage                | Storage method changes    |
| RequestService      | Applies business rules (allowed status transitions)         | Business rules change     |
| RequestValidator    | Validates user-submitted form input                         | Validation rules change   |
| RequestController   | Receives HTTP input, calls services, selects views          | Routes or actions change  |
| ViewRenderer        | Loads a view file and passes variables to it                | Rendering strategy changes|

---

### B5. UML Class Diagram

```
┌──────────────────────┐        ┌──────────────────────┐
│  RequestController   │─uses──▶│   RequestService     │
│──────────────────────│        │──────────────────────│
│ + index(): void      │        │ + create(data): int  │
│ + create(): void     │        │ + changeStatus(...)  │
│ + store(): void      │        └──────────┬───────────┘
└──────────────────────┘                   │ uses
                                           ▼
                               ┌──────────────────────┐
                               │  RequestRepository   │
                               │──────────────────────│
                               │ + all(): array       │
                               │ + find(id): Request  │
                               │ + insert(...): int   │
                               │ + update(...): void  │
                               └──────────┬───────────┘
                                          │ returns
                                          ▼
                               ┌──────────────────────┐
                               │       Request        │
                               │──────────────────────│
                               │ - id: int            │
                               │ - title: string      │
                               │ - description: string│
                               │ - location: string   │
                               │ - status: string     │
                               └──────────────────────┘
```

---

### B6. MVC Mapping Table

| Item                      | MVC Layer  | Why?                                        |
|---------------------------|------------|---------------------------------------------|
| Request (entity)          | Model      | Represents core application data            |
| RequestRepository         | Model      | Handles data access / persistence           |
| RequestService            | Model      | Business rules, no HTTP knowledge           |
| RequestValidator          | Model      | Validates data independently                |
| RequestController         | Controller | Coordinates Model + View via HTTP           |
| Router                    | Controller | Dispatches requests to correct controller   |
| requests/index.php        | View       | Displays list of requests                   |
| requests/show.php         | View       | Displays one request's details              |

---

### B7. Route Table

| HTTP | URL                        | Controller@Action               | Purpose                    |
|------|----------------------------|---------------------------------|----------------------------|
| GET  | /requests                  | RequestController@index         | Show request list          |
| GET  | /requests/create           | RequestController@create        | Show create form           |
| POST | /requests/create           | RequestController@store         | Save new request           |
| GET  | /requests/{id}             | RequestController@show          | View request details       |
| POST | /requests/{id}/status      | RequestController@updateStatus  | Update request status      |
| GET  | /staff/requests            | RequestController@staffIndex    | Staff view all requests    |

---

## PART D — Reflection

**1. Which parts are Model, View, Controller?**
- **Model:** Request, RequestRepository, RequestService, RequestValidator — xử lý data và business logic, không biết gì về HTTP.
- **View:** Các file `.php` trong `app/Views/` — chỉ nhận biến và render HTML.
- **Controller:** RequestController, Router — nhận HTTP input, gọi Model, chọn View.

**2. Validation nên ở đâu?**
Validation nên ở `RequestValidator` (Model layer), được gọi từ Controller trước khi truyền data xuống Service. Lý do: validation là business rule, đặt ở Model thì có thể tái sử dụng từ nhiều nơi (API, CLI, test) mà không cần viết lại.

**3. Nếu đặt SQL trong View thì sao?**
- **Security:** SQL lẫn HTML dễ bị SQL Injection.
- **Maintainability:** Schema DB thay đổi → phải sửa trong từng View file.
- **Testability:** Không thể test logic data mà không render HTML, không test HTML mà không cần DB thật.

**4. Tuần sau sẽ viết gì?**
- Implement `Router.php` đọc Route Table và dispatch đúng controller.
- Viết thật các Controller action thay pseudocode.
- Tạo View templates với HTML + `htmlspecialchars()`.
- Thêm autoloader trong `public/index.php`.
- Kết nối `RequestRepository` với DB thật (PDO).
