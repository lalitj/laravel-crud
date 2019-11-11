@push('scripts')
    <script>
    $(document).ready(function(){
        $(document).on('click','.confirm-prompt', function(e){
            e.preventDefault();
                $this = $(this);
                var link = $(this).attr('href');
                var confirm_text = $(this).data('confirm-text');
                if(!confirm_text){
                    confirm_text = 'Are you sure about '+$(this).text()+"?";
                }
                var cancel_text = $(this).data('cancel-text');
                if(!cancel_text){
                    cancel_text = $this.text()+ ' Process Cancelled!';
                }

                $.confirm({
                    title: $(this).text(),
                    content: confirm_text,
                    buttons: {
                        confirm: function () {
                            window.location.href = ""+link;
                            return false;
                        },
                        cancel: function () {
                            $.alert(cancel_text);
                        }
                        /*somethingElse: {
                            text: 'Something else',
                            btnClass: 'btn-blue',
                            keys: ['enter', 'shift'],
                            action: function(){
                                $.alert('Something else?');
                            }
                        }*/
                    }
                });
            });
    });

</script>
@endpush