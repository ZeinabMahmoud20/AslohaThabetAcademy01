@extends('layouts.app')

<meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="ie=edge" />
      <meta name="description" content="description here" />
      <meta name="keywords" content="keywords,65key" />

@section('title')
لوحة التحكم 
@endsection
@section('styles')
<!-- FontAwesom -->
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
/>
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap"
rel="stylesheet"
/>
<!-- BootStrap5 Css Cdn File -->
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0-beta1/css/bootstrap.rtl.min.css"
/>

      <!-- Custome Css File -->
      <link rel="stylesheet" href="{{ asset('css/main2.css') }}" />
      
@endsection

@section('without_container')
<section class="row g-0">
    <!-- ----------------- Start Aside 
 ----------------- -->
    <aside class="col-2">
       <div class="list-group vh-100 bg-main" id="list-tab" role="tablist">
          <a
             class="list-group-item list-group-item-action active"
            

             
             
             >إدارة المستخدمين</a
          >
          <a
             class="list-group-item list-group-item-action"
           

             
             >إدارة الصلاحيات</a
          >
          <a
             class="list-group-item list-group-item-action"


             >إدارة مديري مكاتب التسويق</a
          >
          <a
             class="list-group-item list-group-item-action"
            
             >
            اضافة مكاتب هندسية             </a
          >
          <a
             class="list-group-item list-group-item-action"
             href="#"
             >
            اضافة مكاتب هندسية تريد التعاقد            
            </a
          >
          <a
             class="list-group-item list-group-item-action"
             href="#"
             >
            اضافة شركات تسويق 
                        
         </a>
       </div>
    </aside>
    <!-- ----------------- Start Main 
 ----------------- -->
    <main class="col-10">
       <div class="tab-content" id="nav-tabContent">
          <!-- ----------------- Start Users 
 ----------------- -->
          <div
             class="tab-pane fade show active"
             id="list-users"
             role="tabpanel"
             aria-labelledby="list-uers-list"
          >
             <section class="users-container px-5 vh-100 justify-content-start vstack">
                <div class="head-content">
                   <div class="main-title my-5">
                      <h2 class="text-center fw-semibold">المستخدمين</h2>
                   </div>
                   <a href=""
                      type="button"
                      class="btn btn-success btn-sm mb-2"
                      data-bs-toggle="tooltip"
                      data-bs-custom-class="custom-tooltip"
                      data-bs-placement="top"
                      title="اضافة مستخدم جديد"
                   >
                      <i class="fa-solid fa-plus"></i>
                   </a>

                   <!-- Start Table -->
                   <div class="table-responsive text-nowrap">
                      <table class="table table-striped overflow-auto">
                         <thead>
                            <tr class="position-sticky top-0 table-light">
                               <th>م</th>
                               <th>الأسم الأول</th>
                               <th>الأسم الأخير</th>
                               <th>البريد الألكتروني</th>
                               <th>الصلاحيات</th>
                               <th>الحالة</th>
                               <th>الأجراءات</th>
                            </tr>
                         </thead>
                         <tbody>


            
                

                <td>
                    <a class="fa-regular fa-eye text-secondary fa-lg showData" href=""></a>
                    <a class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData" href=""></a>

                    <form style="display: inline" method="POST"
                    action="">
                    {{-- <a class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData" --}}
                    {{-- href=""></a> --}}
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border: none; outline:none"
                        class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData">

                        {{-- <input type="submit"> --}}
                    </button>
                    {{-- fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData --}}
                    {{-- {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!} --}}
                </form>



                    {{-- <a class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData" href=""></a> --}}
                    {{-- {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => 'display:inline']) !!}
                    {!! Form::submit('حذف', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!} --}}
                </td>
            </tr>
                

                         </tbody>
                      </table>
                   </div>
                </div>

             </section>
          </div>
          <!-- ----------------- Start Primison 
 ----------------- -->
          <div
             class="tab-pane fade"
             id="list-primison"
             role="tabpanel"
             aria-labelledby="list-primison-list"
          >
          <section class="primison-container px-5 vh-100 justify-content-start vstack">
             <div class="head-content">
                <div class="main-title my-5">
                   <h2 class="text-center fw-semibold">إدارة الصلاحيات</h2>
                </div>
                <a 
                {{-- href="" --}}
                   type="button"
                   class="btn btn-success btn-sm mb-2"
                   data-bs-toggle="tooltip"
                   data-bs-custom-class="custom-tooltip"
                   data-bs-placement="top"
                   title="إنشاء صلاحية جديدة"
                >
                   <i class="fa-solid fa-plus"></i>
                </a>
                <!-- Start Table -->
                <div class="table-responsive text-nowrap">
                   <table class="table table-striped overflow-auto">
                      <thead>
                         <tr class="position-sticky top-0 table-light">
                            <th>م</th>
                            <th class="w-75">الأسم</th>
                            <th>الأجراءات</th>
                         </tr>
                      </thead>
                      <tbody>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                      </tbody>
                   </table>
                </div>
             </div>
             <!-- Pagintion -->
             <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                   <li class="page-item disabled">
                      <a class="page-link">Previous</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">1</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">2</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">3</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">Next</a>
                   </li>
                </ul>
             </nav>
          </section>
          </div>
          <!-- ----------------- Start Markiting 
 ----------------- -->
          <div
             class="tab-pane fade"
             id="list-markiting"
             role="tabpanel"
             aria-labelledby="list-profile-list"
          >
          <section class="markiting-container px-5 vh-100 justify-content-start vstack">
             <div class="head-content">
                <div class="main-title my-5">
                   <h2 class="text-center fw-semibold">مديري التسويق</h2>
                </div>
                <a href="" 

                   type="button"
                   class="btn btn-success btn-sm mb-2"
                   data-bs-toggle="tooltip"
                   data-bs-custom-class="custom-tooltip"
                   data-bs-placement="top"
                   title="إضافة مدير تسويق جديد"
                >
                   <i class="fa-solid fa-plus"></i>
                </a>
                <!-- Start Table -->
                <div class="table-responsive text-nowrap">
                   <table class="table table-striped overflow-auto">
                      <thead>
                         <tr class="position-sticky top-0 table-light">
                            <th>م</th>
                            <th>الأسم الأول</th>
                            <th>الأسم الأخير</th>
                            <th>البريد الألكتروني</th>
                            <th>الصلاحيات</th>
                            <th>الحالة</th>
                            <th>الأجراءات</th>
                         </tr>
                      </thead>
                      <tbody>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>منيسي</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>منيسي</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>منيسي</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>محمد</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>محمد</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>محمد</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         <tr>
                            <td>1</td>
                            <td>أحمد</td>
                            <td>محمد</td>
                            <td>amelmaster16@gmail.com</td>
                            <td>markting</td>
                            <td>مفعل</td>
                            <td>
                               <i
                                  class="fa-regular fa-eye text-secondary fa-lg showData"
                               ></i>
                               <i
                                  class="fa-regular fa-pen-to-square text-primary fa-lg mx-2 editData"
                               ></i>
                               <i
                                  class="fa-regular fa-trash-can text-danger fa-lg mx-2 deleteData"
                               ></i>
                            </td>
                         </tr>
                         
                      </tbody>
                   </table>
                </div>
             </div>
             <!-- Pagintion -->
             <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                   <li class="page-item disabled">
                      <a class="page-link">Previous</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">1</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">2</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">3</a>
                   </li>
                   <li class="page-item">
                      <a class="page-link" href="#">Next</a>
                   </li>
                </ul>
             </nav>
          </section>
          </div>
       </div>
    </main>
 </section>
@endsection

@section('script')
<!-- BootStrap5 Js Cdn File -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0-beta1/js/bootstrap.bundle.min.js"></script>
<!-- Custome Js File -->
<script src="{{ asset('js/main2.js') }} "></script>
@endsection