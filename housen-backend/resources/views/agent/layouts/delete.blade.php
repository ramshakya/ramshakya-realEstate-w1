<style type="text/css">
    .spinner-border{
            width: 1rem;
            height: 1rem;
        }
     .deleteModal{
        top: 150px!important;
    }
</style>
<!-- delete confirmation model  -->
  <div class="modal fade" id="delete_data">
    <div class="modal-dialog modal-sm">
      <div class="modal-content deleteModal">
      
        <!-- Modal Header -->
       
        
        <!-- Modal body -->
        <div class="modal-body">
          This action can wipe your data! Do you want to proceed?<br><br>
          <div class="text-center"><a href="javascript:void(0);" class="btn btn-primary" id="anchor_for_delete" onclick="delete_data(this.id)">yes</a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="#" class="btn btn-secondary" data-dismiss="modal">No</a></div>
        </div>
        
        <!-- Modal footer -->
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div> -->
        
      </div>
    </div>
  </div>
  <!-- end -->
<script type="text/javascript">
	function get_delete_value(delete_id,table_name)
          {
               document.getElementById('anchor_for_delete').setAttribute('data-id',delete_id); 
               document.getElementById('anchor_for_delete').setAttribute('data-table',table_name); 
          }
	function delete_data(clickId)
            {
                var delete_id=document.getElementById(clickId).getAttribute('data-id');
                var table_name=document.getElementById(clickId).getAttribute('data-table');
                var data = {
                    'id':delete_id,
                    'tableName':table_name,
                    "_token": "{{ csrf_token() }}"
            		}
            	
            $.ajax({
              url:'{{url("api/v1/agent/delete-data")}}',
              type:"POST",
              data:data,
              success: function (response) {
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/menu/menuBuilder')}}";
                        location.reload();
                    },2000);
                },
                error:function(status,error){
                    // $('#add_page').attr('disabled', false);
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                    
                }
          }); 
  }
</script>