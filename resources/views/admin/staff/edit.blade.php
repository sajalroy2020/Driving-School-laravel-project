@extends('layouts.app')
@push('title')
    {{$pageTitle}}
@endpush

@section('content')
    <div data-aos="fade-up" data-aos-duration="1000" class="p-sm-30 p-15">
        <div class="p-sm-30 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <!-- Client Information -->
            <div class="tab-pane fade show active" id="pills-ClientInformation" role="tabpanel"
                 aria-labelledby="pills-ClientInformation-tab" tabindex="0">
                <form class="ajax-request" action="{{route('admin.staff.store')}}" method="POST"
                      enctype="multipart/form-data" data-handler="staffEditResponse">
                    @csrf
                    <input type="hidden" name="id" value="{{encrypt($staff->id)}}">
                    <div class="p-sm-25 p-15  bd-ra-10">
                        <!-- Photo -->
                        <div class="pb-30">
                            <div class="upload-img-box profileImage-upload">
                                <div class="icon">
                                    <img class="preview-img" src="{{asset('assets/images/icon/camera.svg')}}" alt=""/>
                                </div>
                                <img src="{{getFileLink($staff->picture)}}"/>
                                <input type="file" name="image" id="zImageUpload" accept="image/*"
                                       onchange="previewFile(this)"/>
                            </div>
                        </div>
                        <div>
                            <h4 class="fs-18 fw-700 lh-24 text-main-color pb-25">{{__('Personal Information')}} :</h4>
                            <!-- Inputs -->
                            <div class="row rg-20">
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asFirstName" class="sf-form-label">{{__('Full Name')}} <span
                                                class="text-danger">*</span></label>
                                        <input value="{{$staff->name}}" type="text" name="name"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter Full Name")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asEmailAddress" class="sf-form-label">{{__('Email Address')}} <span
                                                class="text-danger">*</span></label>
                                        <input value="{{$staff->email}}" type="email" name="email"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter Email Address")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asWhatsAppNumber" class="sf-form-label">{{__('Phone Number')}} <span
                                                class="text-danger">*</span></label>
                                        <input value="{{$staff->phone}}" type="number" name="number"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter WhatsApp Number")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="pb-30 passShowHide">
                                        <label for="inputPassword" class="sf-form-label">{{__("Password")}} <span
                                                class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control sf-form-control passShowHideInput"
                                               id="inputPassword" placeholder="{{__("Enter your password")}}"
                                               name="password"/>
                                        <button type="button" toggle=".passShowHideInput"
                                                class="toggle-password fa-solid fa-eye"></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asZipCode" class="sf-form-label">{{__('Date of Birth')}}
                                            (Y-M-D)<span class="text-danger">*</span></label>
                                        <input value="{{$staff->dob}}" type="date" name="date_of_birth"
                                               class="form-control sf-form-control sf-form-control-2" id="asZipCode"
                                               placeholder="{{__("Date of Birth")}}"/>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asPricing" class="sf-form-label">{{__('Gender')}} <span
                                                class="text-danger">*</span></label>
                                        <select class="sf-select-without-search" name="gender">
                                            <option>{{__("Select Gender")}}</option>
                                            <option
                                                {{$staff->gender == GENDER_MALE ? 'selected' : ''}} value="{{GENDER_MALE}}">{{__('Male')}}</option>
                                            <option
                                                {{$staff->gender == GENDER_FEMALE ? 'selected' : ''}} value="{{GENDER_FEMALE}}">{{__('Female')}}</option>
                                            <option
                                                {{$staff->gender == GENDER_OTHERS ? 'selected' : ''}} value="{{GENDER_OTHERS}}">{{__('Others')}}</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asPricing" class="sf-form-label">{{__('Role')}} <span
                                                class="text-danger">*</span></label>
                                        <select class="sf-select-without-search" name="role">
                                            <option>{{__("Select Role")}}</option>
                                            @foreach($roleList as $role)
                                                <option
                                                    value="{{encrypt($role->id)}}" {{$staff->roles[0]->id == $role->id?'selected':''}}>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asDescription" class="sf-form-label">{{__('Address')}} <span
                                                class="text-danger">*</span></label>
                                        <textarea name="address" class="form-control sf-form-control sf-form-control-2"
                                                  placeholder="{{__("Enter Your Address")}}">{{$staff->address}}</textarea>
                                    </div>
                                </div>
                            </div>

                            <h4 class="fs-18 fw-700 lh-24 text-main-color pb-25 pt-30">{{__('Emergency Contact')}}
                                :</h4>
                            <div class="row rg-20">
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asFirstName"
                                               class="sf-form-label">{{__('Contact Person Name')}} </label>

                                        <input value="{{$staffContact->contact_person??''}}" type="text"
                                               name="contact_name"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter Contact Person Name")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asFirstName" class="sf-form-label">{{__('Contact Phone')}} </label>
                                        <input value="{{$staffContact?->contact_phone}}" type="text"
                                               name="contact_phone"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter Your Contact Phone")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asFirstName"
                                               class="sf-form-label">{{__('Contact Relation')}} </label>
                                        <input value="{{$staffContact->contact_relation??''}}" type="text"
                                               name="contact_relation"
                                               class="form-control sf-form-control sf-form-control-2"
                                               placeholder="{{__("Enter Contact Relation")}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="zForm-wrap">
                                        <label for="asFirstName"
                                               class="sf-form-label">{{__('Contact Address')}} </label>
                                        <textarea name="contact_address"
                                                  class="form-control sf-form-control sf-form-control-2"
                                                  placeholder="{{__("Enter Your Contact Address")}}">{{$staffContact?->contact_address}}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="fs-18 fw-700 lh-24 text-main-color pt-30">{{__('Necessary Document')}} :</h4>
                                <button type="button"
                                        class="border-0 bd-ra-12 py-13 px-25 mt-30 bg-cancel fs-16 fw-600 lh-19 text-main-color addMoreDocument">{{__('Add More')}}</button>
                            </div>

                            @if(count($staffDocument)  > 0)
                                @foreach ($staffDocument as $key => $document)
                                        <div class="row rg-20 mt-25 singleDocumentRow">
                                            <div class="col-md-6">
                                                <div class="zForm-wrap">
                                                    <label for="asDocuemntsType"
                                                           class="sf-form-label">{{__('Document Type')}} </label>
                                                    <input value="{{$document->document_type}}" type="text"
                                                           name="document_type[]"
                                                           class="form-control sf-form-control sf-form-control-2"
                                                           id="asDocumentsName"
                                                           placeholder="{{__("Enter Document Type")}}"/>
                                                </div>
                                                <div class="docuemnt_type"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="zForm-wrap">
                                                    <label for="asDocumentsName"
                                                           class="sf-form-label">{{__('Document Name')}} </label>
                                                    <input value="{{$document->document_name}}" type="text"
                                                           name="document_name[]"
                                                           class="form-control sf-form-control sf-form-control-2"
                                                           id="asDocumentsName"
                                                           placeholder="{{__("Enter Document Name")}}"/>
                                                </div>
                                                <div class="docuemnt_name"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="zForm-wrap">
                                                    <label for="asUploadClientDocuments"
                                                           class="sf-form-label">{{__('Upload Your Document')}} </label>
                                                    <input type="file" name="documents_file[]"
                                                           class="form-control sf-form-control sf-form-control-2"
                                                           accept="image/*,video/*"/>
                                                    <input type="hidden" value="{{$document->document_file_id}}"
                                                           name="documents_file_edit[]">
                                                </div>
                                                <div class="documents_file"></div>
                                            </div>

                                            @if(!is_null($document->document_file_id))
                                                <div class="col-md-6">
                                                    <div class="d-flex justify-content-between">
                                                        @if(in_array(getFileProperty($document->document_file_id, 'extension'), ['jpg','jpeg','png','gif','webp','svg','JPG','JPEG','PNG','GIF','WEBP','SVG']))
                                                            <a href="{{getFileLink($document->document_file_id)}}"
                                                               target="_blank">
                                                                <img src="{{getFileLink($document->document_file_id)}}"
                                                                     alt="file" class="max-w-150 mt-12">
                                                            </a>
                                                        @else
                                                            <a href="{{getFileLink($document->document_file_id)}}"
                                                               class="mt-auto"
                                                               target="_blank">{{__("View Attachment")}}</a>
                                                        @endif
                                                        @if ($key+1 != 1)
                                                            <div>
                                                                <button
                                                                    class="border-0 bd-ra-12 py-13 px-20 mt-30 bg-cancel fs-16 fw-600 lh-19 text-danger removeMoreDocument">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                @endforeach
                            @else
                                <div class="row rg-20">
                                    <div class="col-md-6">
                                        <div class="zForm-wrap">
                                            <label for="asDocuemntsType"
                                                   class="sf-form-label">{{__('Document Type')}} </label>
                                            <input type="text" name="document_type[]"
                                                   class="form-control sf-form-control sf-form-control-2"
                                                   id="asDocumentsName" placeholder="{{__("Enter Documents Type")}}"/>
                                        </div>
                                        <div class="docuemnt_type"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="zForm-wrap">
                                            <label for="asDocumentsName"
                                                   class="sf-form-label">{{__('Documents Name')}} </label>
                                            <input type="text" name="document_name[]"
                                                   class="form-control sf-form-control sf-form-control-2"
                                                   id="asDocumentsName" placeholder="{{__("Enter Documents Name")}}"/>
                                        </div>
                                        <div class="docuemnt_name"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="zForm-wrap">
                                            <label for="asUploadClientDocuments"
                                                   class="sf-form-label">{{__('Upload Your Documents')}} </label>
                                            <input type="file" name="documents_file[]"
                                                   class="form-control sf-form-control sf-form-control-2"
                                                   accept="image/*,.doc,.docx,.pdf"/>
                                        </div>
                                        <div class="documents_file"></div>
                                    </div>
                                </div>
                            @endif
                            <div id="addMoreDocumentDiv">
                            </div>

                        </div>
                    </div>
                    <div class="d-flex align-items-center cg-10 pt-25">
                        <button type="submit"
                                class="border-0 bd-ra-12 py-13 px-25 bg-primary-color fs-16 fw-600 lh-19 text-white">{{__('Save Now')}}</button>
                        <a href="{{url()->previous()}}"
                           class="border-0 bd-ra-12 py-13 px-25 bg-cancel fs-16 fw-600 lh-19 text-main-color">{{__('Back')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <input type="hidden" id="get-document-section-content-route"
           value="{{route('admin.partial.document-section-content')}}">
@endsection
@push('script')
    <script src="{{asset('assets/custom/admin/js/staff.js')}}"></script>
@endpush
