<script>
    function getSelectActivityData() {
        var obj = $('#selected_activity');

        if(obj && obj.val() != 'all'){
            sendData();
        }
    }
</script>