
@extends('template')

@section('content')

    <div id="wrapper">
        <div id="page" class="container">
            <form name="bank" method="post" action="/">

                @csrf <!-- {{ csrf_field() }} -->

                <div>
                    <div class="title">Printing POS Tickets</div>
                    <p>Enter the product code for those POS tickets you need and the corresponding page numbers will appear in the box above</p>
                    <p>Copy and paste the comma separated list into the print dialogue when printing</p>
                    <p>Click Reset when you have finished with the list</p>
                    <div style="margin-top: 15px;">
                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column is-one-fifth">
                                        Product pages:
                                    </div>
                                    <div class="column is-one-fifth">
                                        <textarea id="printPages" rows="4" cols="48" readonly></textarea>
                                        <button id="copyToClipboard" class="button is-success" style="margin-top: 0">Copy to Clipboard</button>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                        Processed codes:
                                    </div>
                                    <div class="column is-one-fifth">
                                        <textarea id="processedCodes" rows="4" cols="48" readonly></textarea>
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
                            <button class="button is-warning" style="margin-top: 0" id="resetTheForm">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script language="javascript" type="application/javascript">
        let productCodes = [];
        let productPages = [];
        let copyToClipboard = $('#copyToClipboard');

        copyToClipboard.click(function(e)
        {
            e.preventDefault();

            let printPages = $('#printPages');
            printPages.focus();
            printPages.select();

            try {
                var successful = document.execCommand('copy');
                if (true === successful) {
                    displayMessage('Pages were copied to the clipboard', 'success');
                } else {
                    displayMessage('Whoops, could not copy to clipboard', 'danger');
                }
            } catch (err) {
                displayMessage('Whoops, could not copy to clipboard due to error', 'danger');
            }
        });

        /**
         * Successful response from the Ajax call, update the form
         * @param responseObject
         */
        const updateForm = function (responseObject)
        {
            let productCode = $('#productCode');
            let printPages = $('#printPages');
            let processedCodes = $('#processedCodes');

            if (true === responseObject.valid) {
                // If ok then add the product code and page to the arrays
                // Otherwise report error to the user and reject the product code

                productCodes.push(productCode.val());
                productPages.push(responseObject.pageNumber);
                let comma = '';
                printPages.text('');
                processedCodes.text('');
                for (let i = 0; i < productCodes.length; i++) {
                    printPages.text(printPages.text() + comma + productPages[i]);
                    processedCodes.text(processedCodes.text() + comma + productCodes[i]);
                    comma = ',';
                }
            } else {
                displayMessage(responseObject.message, 'danger');
            }

            productCode.val('');
        };

        /**
         *  Capture the key up event on the reset button
         */
        $("#resetTheForm").click(function (e) {
            e.preventDefault();
            $('#printPages').text('');
            $('#processedCodes').text('');

            let productCode = $('#productCode');
            productCode.val('');
            productCode.focus();

            productCodes = [];
            productPages = [];
        });

        /**
         *  Capture the key up event on the product code
         */
        $("#productCode").keyup(function (e) {
            e.preventDefault();

            if (event.which === 13) {
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

        $(document).ready(function () {
            $('#productCode').focus();
        });

    </script>

@endsection
