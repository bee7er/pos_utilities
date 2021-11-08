
@extends('template')

@section('content')

    <div id="wrapper">
        <div id="page" class="container">
            <form name="bank" method="post" action="/">

                @csrf <!-- {{ csrf_field() }} -->

                <div>
                    <div class="title">Find page for product code</div>
                    <p>Enter a product code to find the corresponding page number</p>
                    <div style="margin-top: 15px;">
                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column is-one-fifth">
                                        Product code:
                                    </div>
                                    <div class="column is-one-fifth">
                                        <input name="productCode" id="productCode" class="input is-primary is-small"
                                               type="text" placeholder="00000" value="" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column is-one-fifth">
                                        Page:
                                    </div>
                                    <div class="column is-one-fifth">
                                        <input name="pageNumber" id="pageNumber" class="input is-primary is-small"
                                               type="text" readonly>
                                        <div id="description">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(session()->has('message.level'))
                            <div class="alert alert-{{ session('message.level') }}">
                                {!! session('message.content') !!}
                            </div>
                        @endif

                        <div id="apiMessage" class="alert" style="display: none;">&nbsp;</div>

                        <div class="buttons">
                            <button class="button is-warning" id="resetTheForm">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script language="javascript" type="application/javascript">

        /**
         *  Capture the key up event on the reset button
         */
        $("#resetTheForm").click(function (e) {
            e.preventDefault();

            $('#pageNumber').val('');
            $('#description').text('');

            let productCode = $('#productCode');
            productCode.val('');
            productCode.focus();
        });

        /**
         *  Capture the key up event on the product code
         */
        $("#productCode").keyup(function (e) {
            e.preventDefault();

            if (13 === e.which) {
                return;
            }

            let productCode = $('#productCode');
            if (productCode.val().length >= MAX_PRODUCT_CODE_LENGTH) {
                // Find the page number corresponding to the product code
                let formData = {'productCode': productCode.val()};
                let url = "{{config('app.base_url')}}/api/search/" + productCode.val();
                ajaxCall(url, JSON.stringify(formData), updateForm);
            }
        });

        /**
         * Successful response from the Ajax call, update the form
         * @param responseObject
         */
        const updateForm = function (responseObject)
        {
            let pageNumber = $('#pageNumber');
            let description = $('#description');
            let productCode = $('#productCode');

            if (true === responseObject.valid) {
                pageNumber.val(responseObject.pageNumber);
                description.text(responseObject.description);
            } else {
                pageNumber.val('');
                description.text('');
                displayMessage(responseObject.message, 'danger');
            }
            productCode.val('');
        };

        $(document).ready(function () {
            $('#productCode').focus();
        });

    </script>

@endsection
