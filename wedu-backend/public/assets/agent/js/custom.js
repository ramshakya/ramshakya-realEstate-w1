function toastrMesseges(msg, success) {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    if (success) {
        toastr.success(msg);
    } else {
        toastr.error(msg);
    }
}

function edit_wing(id) {
    var html = "";
    $.ajax({
        type: "POST",
        url: '{{url("api/v1/edit-wing")}}',
        data: {
            "id": id
        },
        success: function (res) {
            if (res.status == 200) {
                var data = res.data;
                html = `<div class="card-box  ${data["id"]}" style="width: 100%;"><div class="dropdown float-left ml-2">
                                        <h2><span class=""></span>${data["id"]}</h2>
                                    </div>&nbsp; &nbsp;
                                    <h4 class="header-title mt-0 ml-2">&nbsp;&nbsp;Unit Of All Selected Floors</h4>
                                    <p>701,801</p>
                                    <div dir="ltr" style="height: 450px;" class="morris-chart">
                                        <form id="edit-unit-inventory">
                                        <div class="row ">
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Typography</label>
                                                    <select class="form-control " id="typoSelect" name="typography">
                                                        <option value="">Select Typography</option>
                                                        <option value="1bhk">1bhk</option>
                                                        <option value="2bhk">2bhk</option>
                                                        <option value="3bhk">3bhk</option>
                                                        <option value="penthouse">penthouse</option>
                                                    </select>
                                                <input type="hidden" name="id" value="${data["id"]}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Facing</label>
                                                    <select class="form-control" name="facing" id="facingSelect">
                                                        <option value ="">Select Facing</option>
                                                        <option value ="available">Garden</option>
                                                        <option value ="not available "> Hill </option>
                                                        <option value ="onHold">Road</option>

                                               </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Orientation</label>
                                                    <select class="form-control" name="orientation" id="orientationSelect">
                                                        <option value="">Select Orientation</option>
                                                        <option value="corner">Corner</option>
                                                        <option value="stacked">Stacked</option>

                                                    </select>
                                                </div>
                                                 <div class="form-group">
                                                    <label>Bathrooms</label>
                                                    <input type="text" name="bathrooms" class="form-control" value="${data["bathrooms"]}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Refuge Unit</label>
                                                    <input type="checkbox" name="refuge_unit">

                                            </div>
</div>
                                            <div class="col-md-6 mt-2">
                                                <div class="form-group">
                                                    <label>Unit Status</label>
                                                    <select class="form-control" name="unit_status" id="unitSelect">
                                                        <option value="">Select Units</option>
                                                        <option value="available">Available</option>
                                                        <option value="not_available">Not Available</option>
                                                        <option value="on_hold">On Hold</option>
                                                        <option value="blocked">Blocked</option>
                                                        <option value="sold">Sold</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Directions</label>
                                                    <select class="form-control" name="direction" id="directionSelect">
                                                        <option value="">Select Directions</option>
                                                        <option value="north">North</option>
                                                        <option value="south">South</option>
                                                        <option value="north_east">North East</option>
                                                        <option value="north_west">North West </option>
                                                        <option value="south_east">South East </option>
                                                        <option value="south_west">South West </option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Bedrooms</label>
                                                    <input type="text" name="bedrooms" class="form-control" value="${data["bedrooms"]}"">
                                                </div>
                                                <div class="form-group">
                                                    <label>Premium Unit</label>
                                                    <input type="checkbox" name="premium_unit">
                                                </div>


                                            </div>
                                        </div>
                                        <button class="btn btn-success" type="button" id="edit-unit-value" onclick="submitForm()">Edit</button>
                                    </div>

                                </div>`;


            }
            $(".actionDiv").html("").html(html);
            document.getElementById('typoSelect').value = data["typography"];
            document.getElementById('unitSelect').value = data["unit_status"];
            document.getElementById('facingSelect').value = data["facing"];
            document.getElementById('directionSelect').value = data["direction"];
            document.getElementById('orientationSelect').value = data["orientation"];
            //document.getElementById('refuge').value = data["refuge"];
            //document.getElementById('premiumUnitSelect').value = data["premium_unit"];
            //$(".typoSelect option:selected").val(data["typography"]);


        }
    });
}

/*$("#edit-unit-value").change('click',function () {
alert("clecked");
});*/
function submitForm() {
    /*let formData = new form*/
    var data = $("#edit-unit-inventory").serialize();
    $.ajax({
        type: "POST",
        url: '{{url("api/v1/edit-unit-value")}}',
        data: data,
        success: function (res) {
            //location.reload();
            if (res.status == 422) {
                toastrMesseges(res.errors.errors, false);
            }
            if (res.status == 200) {
                toastrMesseges('Edit Form  Sucessfully !', true);
                location.reload();
            }
            if (res.status == 500) {
                toastrMesseges('Something Went Wrong', false);
            }
        }
    });
}
