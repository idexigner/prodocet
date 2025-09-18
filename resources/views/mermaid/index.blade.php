@extends('layouts.app')

@section('title', 'Diagrama de Flujo')

@section('content')
<div class="content-header">
    <div class="content-title">
        <h1>PRODOCET LMS – Flujo End-to-End</h1>
        <p class="content-subtitle">Diagrama de flujo completo del sistema</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="mermaid">
flowchart TD
    A[Start] --> B[Admin Creates User Profile]
    B --> C[Assign Roles (Student/Teacher/HR/Admin)]
    C --> D[Auth Setup (Username=Email, PW=Last 4 ID)]
    D --> E{User Logs In?}
    E -- No --> D
    E -- Yes --> F[Route to Role Dashboard]

    subgraph Admin_Setup
      F --> G[Create Group (Name/Language/Level/Rate/Modality/Duration)]
      G --> H[Create Curriculum (Name/Language/Level/Topics)]
      H --> I[Assign Teacher (Check Availability)]
      I --> J[Add Students to Group]
      J --> K[Generate Class Schedule (Start/End/Days/Holidays)]
      K --> L[Create Group Calendar]
    end

    L --> M[Notify Students & Teacher]
    M --> N{Student Confirms Enrollment?}
    N -- No --> O[Reminder/Follow-up]
    O --> N
    N -- Yes --> P[Payment (Prepaid Pack or Hourly)]
    P --> Q{Payment Success?}
    Q -- No --> P
    Q -- Yes --> R[Grant Access (Schedule/Links/Topics)]

    R --> S[Upcoming Class Session]
    S --> T[Teacher Conducts Class]
    T --> U[Mark Attendance (Present/Absent/Late/Justified)]
    U --> V[Enter Monthly Skill Grades (CA/CL/IEO/EE 0–50)]
    V --> W{Submitted Within 48h?}
    W -- No --> X[Reminder & Flag Non-Compliance]
    X --> Y[Allow Late Upload (Audit Log)]
    W -- Yes --> Z[Store Records & Update Reports]

    S --> AA{Cancel/Reschedule?}
    AA -- Yes --> AB[Admin Updates Class]
    AB --> L
    AA -- No --> T

    Z --> AC[HR Panel: View Progress/Attendance/Scores]
    AC --> AD{HR Justifies Absence?}
    AD -- Yes --> AE[Record Justification & Notify Admin]
    AD -- No --> AF[No Action]

    Z --> AG[Generate Reports (Group/Student PDF/Excel)]
    Z --> AH[Compute Instructor Hours]
    AH --> AI[Monthly Billing Draft (1.5x 90m, 0.75x No-show)]
    AI --> AJ[Admin Reviews Billing]
    AJ --> AK{Approve Payment?}
    AK -- No --> AL[Return for Correction]
    AL --> AI
    AK -- Yes --> AM[Mark Paid & Lock Records]

    L --> AN{5 Sessions Remaining?}
    AN -- Yes --> AO[Send Alerts (Admin/Teacher/Student/HR)]
    AO --> AP[Plan Renewal/Extension]
    AN -- No --> S

    AM --> AQ[End]
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="module">
        import mermaid from "https://cdn.jsdelivr.net/npm/mermaid@10.9.4/dist/mermaid.esm.min.mjs";
        mermaid.initialize({
            startOnLoad: true,
            securityLevel: "loose",
            theme: "default"
        });
    </script>
@endpush
