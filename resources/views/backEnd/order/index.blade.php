@extends('backEnd.layouts.master')
@section('title', $order_status->name . ' Order')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.order.create') }}" class="btn btn-danger rounded-pill"><i
                                class="fe-shopping-cart"></i> Add New</a>
                    </div>
                    <h4 class="page-title">{{ $order_status->name }} Order ({{ $order_status->orders_count }})</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row order_page">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <ul class="action2-btn">
                                    <li><a data-bs-toggle="modal" data-bs-target="#asignUser"
                                            class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a>
                                    </li>
                                    <li><a data-bs-toggle="modal" data-bs-target="#changeStatus"
                                            class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a>
                                    </li>
                                    <li><a href="{{ route('admin.order.bulk_destroy') }}"
                                            class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete
                                            All</a></li>
                                    <li><a href="{{ route('admin.order.order_print') }}"
                                            class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i>
                                            Print</a></li>
                                    @can('courier')

                                        @if ($steadfast)
                                            <li><a href="{{ route('admin.bulk_courier', 'steadfast') }}"
                                                    class="btn rounded-pill btn-warning multi_order_courier"><i
                                                        class="fe-truck"></i> Courier</a></li>
                                        @endif
                                    @endcan
                                    @can('pathao')
                                        <li><a data-bs-toggle="modal" data-bs-target="#pathao"
                                                class="btn rounded-pill btn-info"><i class="fe-truck"></i> pathao</a></li>
                                    @endcan
                                </ul>
                            </div>
                            <div class="col-sm-4">
                                <form class="custom_form">
                                    <div class="form-group">
                                        <input type="text" name="keyword" placeholder="Search">
                                        <button class="btn  rounded-pill btn-info">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive ">
                            <table id="datatable-buttons" class="table table-striped   w-100">
                                <thead>
                                    <tr>
                                        <th style="width:2%">
                                            <div class="form-check"><label class="form-check-label"><input type="checkbox"
                                                        class="form-check-input checkall" value=""></label>
                                        <th style="width:2%">SL</th>
                        </div>
                        </th>
                        <th style="width:8%">Action</th>
                        <th style="width:8%">Invoice</th>
                        <th style="width:10%">Date</th>
                        <th style="width:10%">Name</th>
                        <th style="width:10%">Phone</th>
                        <th style="width:10%">Assign</th>
                        <th style="width:10%">Amount</th>
                        <th style="width:10%">Status</th>
                        </tr>
                        </thead>


                        <tbody>
                            @foreach ($show_data as $key => $value)
                                <tr data-order-id="{{ $value->id }}"
                                    data-invoice="{{ $value->invoice_id }}"
                                    data-name="{{ $value->shipping ? $value->shipping->name : '' }}"
                                    data-phone="{{ $value->shipping ? $value->shipping->phone : '' }}"
                                    data-address="{{ $value->shipping ? $value->shipping->address : '' }}"
                                    data-amount="{{ $value->amount }}">
                                    <td><input type="checkbox" class="checkbox" value="{{ $value->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="button-list custom-btn-list">
                                            <a href="{{ route('admin.order.invoice', ['invoice_id' => $value->invoice_id]) }}"
                                                title="Invoice"><i class="fe-eye"></i></a>
                                            <a href="{{ route('admin.order.process', ['invoice_id' => $value->invoice_id]) }}"
                                                title="Process"><i class="fe-settings"></i></a>
                                            <a href="{{ route('admin.order.edit', ['invoice_id' => $value->invoice_id]) }}"
                                                title="Edit"><i class="fe-edit"></i></a>
                                            <button type="button" title="Send SMS" class="sms-modal-trigger"
                                                data-bs-toggle="modal" data-bs-target="#sendSmsModal"
                                                data-order-id="{{ $value->id }}"
                                                data-invoice-id="{{ $value->invoice_id }}"
                                                data-confirm-sent="{{ (int) $value->confirm_sms_sent }}"
                                                data-complete-sent="{{ (int) $value->complete_sms_sent }}">
                                                <i class="fe-message-square"></i>
                                            </button>
                                            <form method="post" action="{{ route('admin.order.destroy') }}"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="id">
                                                <button type="submit" title="Delete" class="delete-confirm"><i
                                                        class="fe-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>{{ $value->invoice_id }}</td>
                                    <td>{{ date('d-m-Y', strtotime($value->updated_at)) }}<br>
                                        {{ date('h:i:s a', strtotime($value->updated_at)) }}</td>
                                    <td><strong>{{ $value->shipping ? $value->shipping->name : '' }}</strong>
                                        <p>{{ $value->shipping ? $value->shipping->address : '' }}</p>
                                    </td>
                                    <td>{{ $value->shipping ? $value->shipping->phone : '' }}</td>
                                    <td>{{ $value->user ? $value->user->name : '' }}</td>
                                    <td>৳{{ $value->amount }}</td>
                                    <td>{{ $value->status ? $value->status->name : '' }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    <div class="custom-paginate">
                        {{ $show_data->links('pagination::bootstrap-4') }}
                    </div>
                </div> <!-- end card body-->

            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    </div>
    <!-- Assign User End -->
    <div class="modal fade" id="asignUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.assign') }}" id="order_assign">
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">Select..</option>
                                @foreach ($users as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->

    <div class="modal fade" id="sendSmsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send SMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.send_sms') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="sms_order_id">
                        <div class="form-group mb-3">
                            <label class="form-label">Order</label>
                            <input type="text" class="form-control" id="sms_invoice_id" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="sms_type" class="form-label">SMS Type</label>
                            <select name="sms_type" id="sms_type" class="form-control" required>
                                <option value="">Select..</option>
                                <option value="confirm">Confirm SMS</option>
                                <option value="complete">Complete SMS</option>
                            </select>
                        </div>
                        <small class="text-muted" id="sms_status_note"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Send SMS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Send SMS End-->

    <!-- Assign User End -->
    <div class="modal fade" id="changeStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.status') }}" id="order_status_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="order_status" id="order_status" class="form-control">
                                <option value="">Select..</option>
                                @foreach ($orderstatus as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->
    <div class="modal fade" id="courierConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send To Steadfast</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please check the selected order information before sending to courier.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="courierConfirmRows"></tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-3" id="courierConfirmNote"></small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="confirmCourierSubmit">Confirm Send</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Courier Confirm End-->
    <!-- pathao coureir start -->
    <div class="modal fade" id="pathao" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pathao Courier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.pathao') }}" id="order_sendto_pathao">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pathaostore" class="form-label">Store</label>
                            <select name="pathaostore" id="pathaostore" class="pathaostore form-control">
                                <option value="">Select Store...</option>
                                @if (isset($pathaostore['data']['data']))
                                    @foreach ($pathaostore['data']['data'] as $key => $store)
                                        <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                                    @endforeach
                                @else
                                @endif
                            </select>
                            @if ($errors->has('pathaostore'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pathaostore') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!-- form group end -->
                        <div class="form-group mt-3">
                            <label for="pathaocity" class="form-label">City</label>
                            <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control"
                                style="width:100%">
                                <option value="">Select City...</option>
                                @if (isset($pathaocities['data']['data']))
                                    @foreach ($pathaocities['data']['data'] as $key => $city)
                                        <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                    @endforeach
                                @else
                                @endif
                            </select>
                            @if ($errors->has('pathaocity'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pathaocity') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!-- form group end -->
                        <div class="form-group mt-3">
                            <label for="" class="form-label">Zone</label>
                            <select name="pathaozone" id="pathaozone"
                                class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}"
                                value="{{ old('pathaozone') }}" style="width:100%">
                            </select>
                            @if ($errors->has('pathaozone'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pathaozone') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!-- form group end -->
                        <div class="form-group mt-3">
                            <label for="" class="form-label">Area</label>
                            <select name="pathaoarea" id="pathaoarea"
                                class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}"
                                value="{{ old('pathaoarea') }}" style="width:100%">
                            </select>
                            @if ($errors->has('pathaoarea'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('pathaoarea') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!-- form group end -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- pathao courier  End-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let courierRequest = null;

            @php
                $canResendSms = auth()->check() && auth()->user()->hasAnyRole(['Super Admin', 'super admin', 'super-admin', 'Super admin']);
            @endphp
            const canResendSms = @json($canResendSms);

            $(document).on('click', '.sms-modal-trigger', function() {
                const orderId = $(this).data('order-id');
                const invoiceId = $(this).data('invoice-id');
                const confirmSent = Number($(this).data('confirm-sent')) === 1;
                const completeSent = Number($(this).data('complete-sent')) === 1;

                $('#sms_order_id').val(orderId);
                $('#sms_invoice_id').val('#' + invoiceId);
                $('#sms_type').val('');
                $('#sms_type option[value="confirm"]').prop('disabled', confirmSent && !canResendSms);
                $('#sms_type option[value="complete"]').prop('disabled', completeSent && !canResendSms);

                let notes = [];
                if (confirmSent) {
                    notes.push('Confirm SMS already sent');
                }
                if (completeSent) {
                    notes.push('Complete SMS already sent');
                }
                if (!canResendSms && notes.length > 0) {
                    notes.push('Only super admin can resend sent SMS');
                }

                $('#sms_status_note').text(notes.join(' | '));
            });

            $(".checkall").on('change', function() {
                $(".checkbox").prop('checked', $(this).is(":checked"));
            });

            // order assign
            $(document).on('submit', 'form#order_assign', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var method = $(this).attr('method');
                let user_id = $(document).find('select#user_id').val();

                var order = $('input.checkbox:checked').map(function() {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        user_id,
                        order_ids
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error(res.message || 'Failed something wrong');
                        }
                    }
                });

            });

            // order status change
            $(document).on('submit', 'form#order_status_form', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var method = $(this).attr('method');
                let order_status = $(document).find('select#order_status').val();

                var order = $('input.checkbox:checked').map(function() {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        order_status,
                        order_ids
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error(res.message || 'Failed something wrong');
                        }
                    }
                });

            });
            // order delete
            $(document).on('click', '.order_delete', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function() {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        order_ids
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });

            });

            // multiple print
            $(document).on('click', '.multi_order_print', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function() {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select Atleast One Order!');
                    return;
                }
                $.ajax({
                    type: 'GET',
                    url,
                    data: {
                        order_ids
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            console.log(res.items, res.info);
                            var myWindow = window.open("", "_blank");
                            myWindow.document.write(res.view);
                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });
            });
            // multiple courier
            $(document).on('click', '.multi_order_courier', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const order = $('input.checkbox:checked').map(function() {
                    return $(this).val();
                });
                const order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                let rowsHtml = '';
                let incompleteCount = 0;

                order_ids.forEach(function(orderId) {
                    const row = $('tr[data-order-id="' + orderId + '"]');
                    const invoice = row.data('invoice') || '';
                    const name = row.data('name') || '';
                    const phone = row.data('phone') || '';
                    const address = row.data('address') || '';
                    const amount = row.data('amount') || '';

                    if (!name || !phone || !address) {
                        incompleteCount++;
                    }

                    rowsHtml += `<tr>
                        <td>#${invoice}</td>
                        <td>${name}</td>
                        <td>${phone}</td>
                        <td>${address}</td>
                        <td>৳${amount}</td>
                    </tr>`;
                });

                $('#courierConfirmRows').html(rowsHtml);
                $('#courierConfirmNote').text(
                    incompleteCount > 0
                        ? incompleteCount + ' selected order has missing customer info. Please review before sending.'
                        : 'All selected orders look ready for courier.'
                );

                courierRequest = {
                    url: url,
                    order_ids: order_ids
                };

                const courierModal = new bootstrap.Modal(document.getElementById('courierConfirmModal'));
                courierModal.show();
            });

            $(document).on('click', '#confirmCourierSubmit', function() {
                if (!courierRequest) {
                    toastr.error('No order selected for courier.');
                    return;
                }

                const button = $(this);
                button.prop('disabled', true).text('Sending...');

                $.ajax({
                    type: 'GET',
                    url: courierRequest.url,
                    data: {
                        order_ids: courierRequest.order_ids
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error(res.message || 'Failed something wrong');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Courier request failed.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        }

                        toastr.error(message);
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Confirm Send');
                    }
                });
            });

            $('#courierConfirmModal').on('hidden.bs.modal', function() {
                courierRequest = null;
                $('#courierConfirmRows').html('');
                $('#courierConfirmNote').text('');
                $('#confirmCourierSubmit').prop('disabled', false).text('Confirm Send');
            });
        })
    </script>
@endsection
