@include('core.header')
<div id="pickDateContainer"></div>
<div id="formErrors" style="display: none;"></div>
<div id="step1" style="margin-top: 30px;">
    <div class="row">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 1:</span> Where are you? <small>(Lab, Office Hours etc...)</small></h3>
            <hr />
            <select id="inputLocation" class="form-control" name="inputLocation">
                @foreach ($types as $type)
                <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button id="btnToStep2" data-nid="2" class="btn btn-primary btnNextStep">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step2" style="margin-top: 30px; display: none;">
    <div class="row">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 2:</span> What is the date and <span style="font-style: italic">start</span> time of this <span id="step2Location">Lab</span></h3>
            <hr />
            <input type="text" class="form-control" name="inputDate" id="inputDate" placeholder="Date" readonly />
            <input style="margin-top: 5px;" type="text" class="form-control" name="inputTime" id="inputTime" placeholder="Start Time" readonly />
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button id="btnBackToStep1" data-pid="1" class="btn btn-default btnPrevStep">Back <i class="fa fa-arrow-left fa-fw"></i></button>
            <button id="btnToStep3" data-nid="3" class="btn btn-primary btnNextStep">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step3" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 3:</span> What GSI (TA) or Tutor are you working under today?</h3>
            <hr />
            <select class="form-control" name="inputGSI" id="inputGSI">
                @foreach ($tas as $ta)
                    <option value="{{{ $ta->id }}}">{{{ $ta->name }}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button disabled="disabled" id="btnBackToStep2" data-pid="2" class="btn btn-default btnPrevStep">Back <i class="fa fa-arrow-left fa-fw"></i></button>
            <button disabled="disabled" id="btnToStep4" data-nid="4" class="btn btn-primary btnNextStep">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step4" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 4:</span> Is this a makeup check in? <small>(Is this check in to make up a skipped session?)</small></h3>
            <hr />
            <select class="form-control" name="inputMakeup" id="inputMakeup">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button disabled="disabled" id="btnBackToStep3" data-pid="3" class="btn btn-default btnPrevStep">Back <i class="fa fa-arrow-left fa-fw"></i></button>
            <button disabled="disabled" id="btnToStep5" data-nid="5" class="btn btn-primary btnNextStep">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step5" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 5:</span> What is the unique secret word? <small>(Have your GSI enter this.)</small></h3>
            <hr />
            <input id="inputPassword" class="form-control" type="password" />
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button disabled="disabled" id="btnBackToStep4" data-pid="4" class="btn btn-default btnPrevStep">Back <i class="fa fa-arrow-left fa-fw"></i></button>
            <button id="submitCheckInForm" disabled="disabled" data-nid="6" data-submit-form="true" class="btn btn-success">Complete Check In <i id="checkInLoader" class="fa fa-check-circle-o fa-fw"></i></button>
            <button disabled="disabled" style="display: none;" id="btnToStep6" data-nid="6" class="btn btn-success btnNextStep">Complete Check In <i id="checkInLoader" class="fa fa-check-circle-o fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step6" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <h3>Lab assistant check in complete.</h3>
        <hr />
    </div>
</div>
<div class="boxLoading" style="display: none;"></div>
@section('js')

   $('#inputPassword').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == 13 && !($('#submitCheckInForm').is(':disabled'))) {
            $('#submitCheckInForm').click();
        }
   });
   $("#submitCheckInForm").on("click", function() {
        var btn = $(this);
        btn.attr("disabled", true);
        $('#formErrors').hide();
        $('#checkInLoader').addClass("fa-spin");
        var _token = "{{ csrf_token() }}";
        var location = $('#inputLocation').val();
        var date = $('#inputDate').val();
        var time = $('#inputTime').val();
        var gsi = $('#inputGSI').val();
        var makeup = $('#inputMakeup').val();
        var password = $('#inputPassword').val();
        $.ajax({
             method: "POST",
             url: "{{ URL::route("dolacheckin") }}",
             data: {
                _token: _token,
                location: location,
                date: date,
                time: time,
                gsi: gsi,
                makeup: makeup,
                password: password,
            }
        })
            .done(function(received) {
                $('#checkInLoader').removeClass("fa-spin");
                if (received != 1) {
                    $('#formErrors').html(received).show();
                    btn.attr("disabled", false);
                }
                else {
                    $('#btnToStep6').click();
                }
            });

   });
   $('#inputLocation').on("change", function() {
        var val = $(this).val();
        var location = $('#inputLocation option[value=' + val + ']').text();
        $('#step2Location').html(location);
   });
   $('#inputDate').pickadate({
        "container": "#pickDateContainer",
   });
   $('#inputTime').pickatime({
        "container": "#pickDateContainer",
   });
   $('.btnNextStep').on('click', function() {
        $(this).prop("disabled", true);
        $(this).siblings("button").prop("disabled", true);
        var nid = parseInt($(this).attr("data-nid"));
        var cid = nid - 1;
        var currentStep = $('#step' + cid);
        var nextStep = $('#step' + nid);
        $('.boxLoading').fadeIn();
        currentStep.addClass('animated fadeOutUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            $('.boxLoading').fadeOut();
            currentStep.hide().removeClass('animated fadeOutUp');
            nextStep.show().addClass('animated fadeInUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
               $(this).removeClass('animated fadeInUp');
               $(this).find("button").prop("disabled", false);
            });
        });
   });
   $('.btnPrevStep').on('click', function() {
        $(this).prop("disabled", true);
        $(this).siblings("button").prop("disabled", true);
        $('.boxLoading').fadeIn();
        var pid = parseInt($(this).attr("data-pid"));
        var cid = pid + 1;
        var currentStep = $('#step' + cid);
        var prevStep = $('#step' + pid);
        currentStep.addClass('animated fadeOutDown').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            $('.boxLoading').fadeOut();
            currentStep.hide().removeClass('animated fadeOutDown');
            prevStep.show().addClass('animated fadeInDown').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass('animated fadeInDown');
                $(this).find("button").prop("disabled", false);
           });
       });
   });
@endsection
@include('core.footer')