@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <form name="pos" method="post" action="/admin">

                    {{ csrf_field() }}

                    <div class="card-body">

                        @if(session()->has('message.level'))
                            <div class="alert alert-{{ session('message.level') }}">
                                {!! session('message.content') !!}
                            </div>
                        @endif

                        <div style="font-weight: bold; margin-bottom: 20px;">
                            1. When notified of changes to the Product Price PDF we must update the database to reflect
                            these changes
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column" style="padding-left: 30px;">
                                        Download the latest version of Product Price PDF
                                    </div>
                                    <div class="column">
                                        <input name="productsDataUrl" id="productsDataUrl" class="input is-primary
                                        is-small" type="text" value="{{ $productsDataUrl }}" required>
                                    </div>
                                    <div class="column">
                                        <button type="button" class="btn btn-primary" id="downloadProductsButton">
                                            {{ __('Download') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-weight: bold; margin-bottom: 20px;">
                            2. Having obtained the latest version of the Product Price PDF, use the following option to
                            update the database
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column" style="padding-left: 30px;">
                                        Refresh Product Price table - <span class="refresh-warning">NB Could take 10 minutes</span>
                                    </div>
                                    <div class="column">&nbsp;</div>
                                    <div class="column">
                                        <button type="button" class="btn btn-primary" id="refreshProductsButton">
                                            {{ __('Refresh Data') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-weight: bold; margin-bottom: 20px;">
                            3. Verify the database update
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column" style="padding-left: 30px;">
                                        Verify Product Price table
                                    </div>
                                    <div class="column">&nbsp;</div>
                                    <div class="column">
                                        <button type="button" class="btn btn-primary" id="verifyProductsButton">
                                            {{ __('Verify Data') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-weight: bold; margin-bottom: 20px;">
                            4. Check effective date
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column" style="padding-left: 30px;">
                                        Effective date: <span id="effectiveDate" style="font-weight: bold; margin-left: 20px;"></span>
                                    </div>
                                    <div class="column">&nbsp;</div>
                                    <div class="column">
                                        <button type="button" class="btn btn-primary" id="effectiveDateButton">
                                            {{ __('Check Date') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-weight: bold; margin-bottom: 20px;">
                            5. View the PDF
                        </div>

                        <div class="field">
                            <div class="control">
                                <div class="columns">
                                    <div class="column" style="padding-left: 30px;">
                                        Click here to show the PDF contents:
                                    </div>
                                    <div class="column">&nbsp;</div>
                                    <div class="column">
                                        <button type="button" class="btn btn-primary" id="viewPdf">
                                            {{ __('View PDF') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div id="apiMessage" class="alert" style="display: none;">&nbsp;</div>

                        <div class="links">
                            <hr />
                            <div><a href="/">Go to the front end</a></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script language="javascript" type="application/javascript">

    /**
     * Working message
     */
    const viewPdf = function ()
    {
        // get the url from (1) and use that to view pdf
        alert('continue...');

    };

    /**
     * Working message
     */
    const workingMessage = function ()
    {
        let apiMessage = $('#apiMessage');
        apiMessage.text('Working please wait ...');
        // Clear previous classes
        apiMessage.removeClass(['alert-success', 'alert-danger']);
        apiMessage.addClass('alert-success');
        apiMessage.fadeIn(1000);
    };


    /**
     *  Capture the click event on the download button
     */
    $("#viewPdf").click(function (e) {
        e.preventDefault();

        let productsDataUrl = $('#productsDataUrl').val();
        // Open PDF in a new tab
        window.open(productsDataUrl);
    });

    /**
     *  Capture the click event on the download button
     */
    $("#downloadProductsButton").click(function (e) {
        e.preventDefault();

        workingMessage();
        let productsDataUrl = $('#productsDataUrl').val();
        let url = "{{config('app.base_url')}}/api/download";
        ajaxCall(url, JSON.stringify({"productsDataUrl": productsDataUrl}), productsProcessed);
    });

    /**
     *  Capture the click event on the refresh button
     */
    $("#refreshProductsButton").click(function (e) {
        e.preventDefault();

        workingMessage();
        let url = "{{config('app.base_url')}}/api/refresh";
        ajaxCall(url, JSON.stringify({}), productsProcessed);
    });

    /**
     *  Capture the click event on the verify button
     */
    $("#verifyProductsButton").click(function (e) {
        e.preventDefault();

        workingMessage();
        let url = "{{config('app.base_url')}}/api/verify";
        ajaxCall(url, JSON.stringify({}), productsProcessed);
    });

    /**
     * Process response from the Ajax call
     * @param responseObject
     */
    const productsProcessed = function (responseObject)
    {
        if (responseObject.valid) {
            displayMessage(responseObject.message, 'success');
        } else {
            displayMessage(responseObject.message, 'danger');
        }
    };

    /**
     *  Capture the click event on the effective date button
     */
    $("#effectiveDateButton").click(function (e) {
        e.preventDefault();

        workingMessage();
        let productCode = {'productCode': '14666'};    // Guv'nor
        let url = "{{config('app.base_url')}}/api/search/14666";
        ajaxCall(url, JSON.stringify([productCode]), updateForm);
    });

    /**
     * Successful response from the Ajax call, update the form
     * @param responseObject
     */
    const updateForm = function (responseObject)
    {
        let effectiveDate = $('#effectiveDate');
        effectiveDate.text(responseObject.effectiveDate);
        displayMessage('Effective date handled', 'success');
    };

</script>
@endsection
