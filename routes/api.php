<?php



$router->get('users', 'UserController@index');



//Login Endpoints
$router->post('students-login', 'AuthController@Studentlogin');
$router->post('teachers-login', 'AuthController@Teacherlogin');
$router->post('staffs-login', 'AuthController@Stafflogin');
$router->post('admins-login', 'AuthController@Adminlogin');
$router->post('super-admins-login', 'AuthController@SupedAdminlogin');



    // Add more routes as needed


$router->group(['middleware' => 'auth.token'], function () use ($router) {
   //Logout Endpoints
    $router->post('students-logout', 'AuthController@Studentlogout');
    $router->post('teachers-logout', 'AuthController@Teacherlogout');
    $router->post('staffs-logout', 'AuthController@Stafflogout');
    $router->post('admins-logout', 'AuthController@Adminlogout');
    


    //check if user is authenticated
    $router->get('students-check-auth', 'AuthController@checkAuthStudent');
    $router->get('teacher-check-auth', 'AuthController@checkAuthTeacher');
    $router->get('staff-check-auth', 'AuthController@checkAuthStaff');
    $router->get('admin-check-auth', 'AuthController@checkAuthAdmin');
    
    //live lesson
    $router->get('live-lessons', 'LessonController@liveLessons');

    //video recordings
    $router->get('video-recordings', 'VideoRecordingController@videoRecordings');

    //Class Tute Books
    $router->get('class-tute-books', 'ClassTuteController@ClassTuteBooks');

    $router->post('get-subject', 'SubjectController@getSubject');
    //Super Admin Endpoints
    //Auth Endpoints
    $router->get('superadmin-check-auth', 'AuthController@checkAuthSuperAdmin');
    $router->post('super-admins-logout', 'AuthController@SupedAdminlogout');

    //Financial Endpoints
    $router->get('income-expense-summery', 'Finance\IncomeExpencesController@summery');
    $router->get('income-expense-summery-chart', 'Finance\IncomeExpencesController@summeryChart');
    ////Receipt Category
    $router->get('receipt-categories', 'Finance\ReceiptCategoryController@index');
    $router->post('receipt-categories', 'Finance\ReceiptCategoryController@store');
    $router->get('receipt-categories/{id}', 'Finance\ReceiptCategoryController@show');
    $router->get('receipt-categories/{id}/edit', 'Finance\ReceiptCategoryController@edit');
    $router->post('receipt-categories/{id}', 'Finance\ReceiptCategoryController@update');
    $router->delete('receipt-categories/{id}', 'Finance\ReceiptCategoryController@destroy');
    $router->post('receipt-categories/{id}/restore', 'Finance\ReceiptCategoryController@restore');
    ////Payment Category
    $router->get('payment-categories', 'Finance\PaymentCategoryController@index');
    $router->post('payment-categories', 'Finance\PaymentCategoryController@store');
    $router->get('payment-categories/{id}', 'Finance\PaymentCategoryController@show');
    $router->get('payment-categories/{id}/edit', 'Finance\PaymentCategoryController@edit');
    $router->post('payment-categories/{id}', 'Finance\PaymentCategoryController@update');
    $router->delete('payment-categories/{id}', 'Finance\PaymentCategoryController@destroy');
    $router->post('payment-categories/{id}/restore', 'Finance\PaymentCategoryController@restore');


    ////Tax Payment Withholding
    $router->get('tax-payment-withholding', 'Finance\TaxPaymentWithholdingController@index');
    $router->post('add-tax-payment-withholding', 'Finance\TaxPaymentWithholdingController@store');
    $router->get('tax-payment-withholding/{id}', 'Finance\TaxPaymentWithholdingController@show');
    $router->get('tax-payment-withholding/{id}/edit', 'Finance\TaxPaymentWithholdingController@edit');
    $router->post('update-tax-payment-withholding/{id}', 'Finance\TaxPaymentWithholdingController@update');
    $router->delete('delete-tax-payment-withholding/{id}', 'Finance\TaxPaymentWithholdingController@destroy');
    $router->post('restore-tax-payment-withholding/{id}', 'Finance\TaxPaymentWithholdingController@restore');

    ////Income Tax
    $router->get('income-tax', 'Finance\IncomeTaxController@index');
    $router->post('add-income-tax', 'Finance\IncomeTaxController@store');
    $router->get('income-tax/{id}', 'Finance\IncomeTaxController@show');
    $router->get('income-tax/{id}/edit', 'Finance\IncomeTaxController@edit');
    $router->post('update-income-tax/{id}', 'Finance\IncomeTaxController@update');
    $router->delete('delete-income-tax/{id}', 'Finance\IncomeTaxController@destroy');
    $router->post('restore-income-tax/{id}', 'Finance\IncomeTaxController@restore');

    ////Cash Balance
    $router->get('cash-balance', 'Finance\CashBalanceController@index');
    $router->get('add-cash-balance', 'Finance\CashBalanceController@store');
    $router->get('update-cash-balance/{id}', 'Finance\CashBalanceController@update');
    $router->get('delete-cash-balance/{id}', 'Finance\CashBalanceController@destroy');
    ////Bank Deposit
    $router->get('bank-deposit', 'Finance\BankDepositController@index');
    $router->get('add-bank-deposit', 'Finance\BankDepositController@store');
    $router->get('update-bank-deposit/{id}', 'Finance\BankDepositController@update');
    $router->get('delete-bank-deposit/{id}', 'Finance\BankDepositController@destroy');
    ////Bank Withdraw
    $router->get('bank-withdraw', 'Finance\BankWithdrawController@index');
    $router->get('add-bank-withdraw', 'Finance\BankWithdrawController@store');
    $router->get('update-bank-withdraw/{id}', 'Finance\BankWithdrawController@update');
    $router->get('delete-bank-withdraw/{id}', 'Finance\BankWithdrawController@destroy');
    ////Petty Cash
    $router->get('petty-cash', 'Finance\PettyCashController@index');
    $router->get('petty-cash/{id}', 'Finance\PettyCashController@show');
    $router->get('add-petty-cash', 'Finance\PettyCashController@store');
    $router->get('update-petty-cash/{id}', 'Finance\PettyCashController@update');
    $router->get('delete-petty-cash/{id}', 'Finance\PettyCashController@destroy');

    //Employee Endpoints
    ////Employee Details
    $router->get('employees', 'Employee\EmployeeController@index');
    $router->get('employee/{id}', 'Employee\EmployeeController@show');
    $router->post('employee-add', 'Employee\EmployeeController@store');
    $router->put('employee-update/{id}', 'Employee\EmployeeController@update');
    $router->delete('employee-delete/{id}', 'Employee\EmployeeController@destroy');
    $router->post('employee/staff-register', 'Employee\EmployeeController@Staffregister');
    
    ////Employee Salary
    $router->get('employee-salary', 'Employee\EmployeeSalaryController@index');
    $router->get('employee-salary/{id}', 'Employee\EmployeeSalaryController@show');
    $router->get('employee-salary-add', 'Employee\EmployeeSalaryController@store');
    $router->get('employee-salary-update/{id}', 'Employee\EmployeeSalaryController@update');
    $router->get('employee-salary-delete/{id}', 'Employee\EmployeeSalaryController@destroy');
    ////Employee Leave
    $router->get('employee-leave', 'Employee\EmployeeLeaveController@index');
    $router->get('employee-leave/{id}', 'Employee\EmployeeLeaveController@show');
    $router->get('employee-leave-add', 'Employee\EmployeeLeaveController@store');
    $router->get('employee-leave-update/{id}', 'Employee\EmployeeLeaveController@update');
    $router->get('employee-leave-delete/{id}', 'Employee\EmployeeLeaveController@destroy');
    ////Employee Attendence
    $router->get('employee-attendence', 'Employee\EmployeeAttendenceController@index');
    $router->get('employee-attendence/{id}', 'Employee\EmployeeAttendenceController@show');
    $router->get('employee-attendence-add', 'Employee\EmployeeAttendenceController@store');
    $router->get('employee-attendence-update/{id}', 'Employee\EmployeeAttendenceController@update');
    $router->get('employee-attendence-delete/{id}', 'Employee\EmployeeAttendenceController@destroy');

    //Special Approval Endpoints
    $router->get('special-approval', 'Special\SpecialApprovalController@index');
    $router->get('special-approval/{id}', 'Special\SpecialApprovalController@show');
    $router->get('special-approval-add', 'Special\SpecialApprovalController@store');
    $router->get('special-approval-update/{id}', 'Special\SpecialApprovalController@update');
    $router->get('special-approval-delete/{id}', 'Special\SpecialApprovalController@destroy');

    //Create Account Endpoints
    $router->get('create-account', 'Account\CreateAccountController@index');
    $router->get('create-account/{id}', 'Account\CreateAccountController@show');
    $router->get('create-account-add', 'Account\CreateAccountController@store');
    $router->get('create-account-update/{id}', 'Account\CreateAccountController@update');
    $router->get('create-account-delete/{id}', 'Account\CreateAccountController@destroy');

    //Student Approval Endpoints
    ////Scholarship
    $router->get('scholarship', 'StudentApproval\ScholarshipController@index');
    $router->get('scholarship/{id}', 'StudentApproval\ScholarshipController@show');
    $router->get('scholarship-add', 'StudentApproval\ScholarshipController@store');
    $router->get('scholarship-update/{id}', 'StudentApproval\ScholarshipController@update');
    $router->get('scholarship-delete/{id}', 'StudentApproval\ScholarshipController@destroy');
    ////Other Grade
    $router->get('other-grade', 'StudentApproval\OtherGradeController@index');
    $router->get('other-grade/{id}', 'StudentApproval\OtherGradeController@show');
    $router->get('other-grade-add', 'StudentApproval\OtherGradeController@store');
    $router->get('other-grade-update/{id}', 'StudentApproval\OtherGradeController@update');
    $router->get('other-grade-delete/{id}', 'StudentApproval\OtherGradeController@destroy');
    ////Free Video
    $router->get('free-video', 'StudentApproval\FreeVideoController@index');
    $router->get('free-video/{id}', 'StudentApproval\FreeVideoController@show');
    $router->get('free-video-add', 'StudentApproval\FreeVideoController@store');
    $router->get('free-video-update/{id}', 'StudentApproval\FreeVideoController@update');
    $router->get('free-video-delete/{id}', 'StudentApproval\FreeVideoController@destroy');


    
});
$router->group(['middleware' => 'auth.role:staff,superadmin,admin', 'namespace' => 'Globle'], function () use ($router) {
   
    $router->get('grades/create', 'GradeController@create'); // GET /grades/create
    $router->post('grades', 'GradeController@store'); // POST /grades
    
    $router->get('grades/{grade}/edit', 'GradeController@edit'); // GET /grades/{grade}/edit
    $router->put('grades/{grade}', 'GradeController@update'); // PUT /grades/{grade}
    //$router->patch('grades/{grade}', 'GradeController@update'); // PATCH /grades/{grade}
    $router->delete('grades/{grade}', 'GradeController@destroy'); // DELETE /grades/{grade}

   

    
});


$router->group(['middleware' => 'auth.role:staff,superadmin,admin,teacher'], function () use ($router) {
   
     $router->get ('class-tute-teacher', 'ClassTuteController@classTuteTeacher');
     $router->post ('class-tute-store', 'ClassTuteController@classTuteTeacherStore');
     $router->get ('class-tute-destroy', 'ClassTuteController@classTuteTeacherDestroy');


     $router->get ('class-paper-teacher', 'ClassPaperController@classPaperTeacher');
     $router->post ('class-paper-store', 'ClassPaperController@classPaperTeacherStore');
     $router->get ('class-paper-destroy', 'ClassPaperController@classPaperTeacherDestroy');


     $router->get ('teacher-subjects', 'TeacherSubjectController@teacherSubjects');
     $router->get ('teacher-subjects-count', 'TeacherSubjectController@teacherSubjectsCount');
     $router->get('subjects{id}', 'Globle\SubjectController@show');

     $router->get ('teacher-class-note', 'ClassNoteController@teacherNoteList');
     $router->get ('teacher-class-note-store', 'ClassNoteController@teacherNoteStore');
     $router->get ('teacher-class-note-update', 'ClassNoteController@teacherNoteUpdate');
     $router->get ('teacher-class-note-destroy', 'ClassNoteController@teacherNoteDestroy');

     $router->get ('pending-class-note', 'ClassNoteController@teacherNotePending');
     $router->get ('approved-class-note', 'ClassNoteController@teacherNoteApproved');


     $router->post('register-teacher', 'AuthController@Teacherregister'); 

   

    
});
$router->group(['middleware' => 'auth.role:staff,superadmin,admin'], function () use ($router) {
$router->get('payment-history', 'PaymentController@StudentPaymentHistory');
$router->get('student-payment-history', 'PaymentController@SingleStudentPaymentHistory');


$router->get('payment-bank-history', 'PaymentController@StudentBankPaymentHistory');

$router->get('pending-payments', 'PaymentController@StudentpendingPayment');
$router->get('approved-payments', 'PaymentController@StudentapprovedPayment');

$router->post('approve-payment', 'PaymentController@approvePayment');
$router->post('reject-payment', 'PaymentController@rejectPayment');

$router->get('intro-student', 'IntroVideoController@student');
$router->get('intro-teacher', 'IntroVideoController@teacher');
$router->get('intro-staff', 'IntroVideoController@staff');
$router->get('intro-admin', 'IntroVideoController@admin');
$router->post('create_subject', 'Globle\SubjectController@create');
$router->post('update_subject', 'Globle\SubjectController@update');
$router->post('delete_subject', 'Globle\SubjectController@destroy');

$router->get('class-issues', 'ClassIssuesController@index');
$router->get('video-issues', 'ClassIssuesController@Videoindex');

$router->post('class-remark', 'ClassIssuesController@remark');
$router->post('video-remark', 'ClassIssuesController@Videoremark');

$router->post('intro-video-teacher','TeacherIntroController@teacherIntro');

$router->get('item-categories-index','IteamShopController@itemCategoriesIndex');
$router->post('item-categories-store','IteamShopController@itemCategoriesStore');
$router->post('item-categories-update','IteamShopController@itemCategoriesUpdate');
$router->post('item-categories-delete','IteamShopController@itemCategoriesDelete');

$router->get('all-staff','UserController@allStaff');

$router->get('item-index','IteamShopController@Index');
$router->post('item_store','IteamShopController@Store');
$router->post('item_update','IteamShopController@Update');
$router->post('item_delete','IteamShopController@iDelete');






});

$router->group(['middleware' => 'auth.role:staff,student,superadmin,admin,teacher'], function () use ($router) {
     $router->post('student-attendence', 'AttendenceController@StudentAttendence'); // qr code
     $router->post('student-attendances', 'AttendenceController@StudentAttendances');
     $router->get('student-attendances-data', 'AttendenceController@StudentAttendenceData');
     //teacher live lesson
     $router->get('live-lessons-teacher', 'LessonController@teacherliveLessons');
     $router->get('live-lessons-show', 'LessonController@liveLessonsshow');
     $router->post('live-lessons-update', 'LessonController@liveLessonsUpdate');
     $router->get('video-recordings-teacher', 'VideoRecordingController@videoRecordingsTeacher');
     $router->post('video-recordings-update', 'VideoRecordingController@videoRecordingsUpdate');


     $router->get('grades/{grade}', 'Globle\GradeController@show'); // GET /grades/{grade}

     $router->get('student-subjects', 'SubjectController@studentSubject');
     $router->get('student-subjects-term', 'SubjectController@studentSubjectTerm');

     $router->get('student-certificate', 'CertificateController@index');
     $router->post('student-certificate-upload', 'CertificateController@studentCertificateUpload');

     $router->get('note-paper-list', 'ClassNoteController@studentNoteList');
     $router->get('note-paper-count', 'ClassNoteController@studentNoteCount');
     $router->post('note-paper-list', 'ClassNoteController@studentNoteStore');
     $router->post('class-note-paper-upload', 'ClassNoteController@studentNotePaperUpload');


     $router->post('term-test-upload', 'StudenttermPaperController@studentTermPaperUpload');

     $router->get('class-paper', 'ClassPaperController@ClassPaper');


     $router->get('student-subjects-get', 'StudentSubjectController@studentSubjectGet');
     $router->post('remove-subject', 'StudentSubjectController@studentSubjectRemove');
     $router->post('add-subject', 'StudentSubjectController@studentSubjectAdd');
     $router->get('grade_wise_subjects', 'Globle\SubjectController@GradeWiseSubjects');



     $router->get('student-reviwe-teacher', 'StudentReviewController@studentReviewTeacher');
     $router->post('student-bank-payment', 'PaymentController@studentBankPayment');
    
    $router->post('manual-payment', 'PaymentController@studentManualPayment');

    $router->post('student-card-payment', 'PaymentController@studentCardPayment');


    $router->post('payment-history-search', 'PaymentController@PaymentHistorySearch');
    $router->get('students-search', 'SearchController@StudentSearch');
    $router->get('single-student', 'SearchController@SingleStudent');
    $router->get('all-student', 'SearchController@AllStudent');

    $router->get('all-teacher', 'SearchController@AllTeacher');


    $router->get('single-teacher', 'SearchController@SingleTeacher');
    

    $router->post('/create_grade', 'Globle\GradeController@store');
    $router->post('/update_grade', 'Globle\GradeController@update');
    $router->post('/remove_grade','Globle\GradeController@destroy');

    
    






});
//Common Endpoints
$router->get('grades', 'Globle\GradeController@index'); // GET /grades
$router->get('subjects', 'Globle\SubjectController@index');
$router->post('send-otp', 'Globle\SmsController@sendOtp');
$router->post('send-sms', 'Globle\SmsController@sms');
$router->post('register-student', 'AuthController@Studentregister');    




$router->options('/{any:.*}', function () {
    return response('OK', 200);
});