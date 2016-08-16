/**
* File: CMSMain.RegionForm.js
*/
(function($) {
    $.entwine('ss', function($){
        // Add page form
        $(".cms-add-form #Form_AddForm_PageTypeFake li").entwine({
            onclick: function(e) {
                this.setSelected(true);
            },
            setSelected: function(bool) {
                var input = this.find('input');
                if(bool && !input.is(':disabled')) {
                    this.siblings().setSelected(false);
                    this.toggleClass('selected', true);
                    input.prop('checked', true);
                    values = input.val().split('-');
                    $('#Form_AddForm_PageType').val(values[0]);
                    $('#Form_AddForm_PageTypeID').val(values[1]);
                    $('.cms-add-form button[name=action_doAdd]').button('enable');
                } else {
                    this.toggleClass('selected', false);
                    input.prop('checked', false);
                }
            },
            setEnabled: function(bool) {
                $(this).toggleClass('disabled', !bool);
                if(!bool) $(this).find('input').attr('disabled',  'disabled').removeAttr('checked');
                else $(this).find('input').removeAttr('disabled');
            }
        });

        // Edit settings
        $(".cms-edit-form #Form_EditForm_PageTypeFake").chosen().entwine({
            onchange: function(e) {
                values = this.chosen().val().split('-');
                $('#Form_EditForm_ClassName').val(values[0]);
                $('#Form_EditForm_PageTypeID').val(values[1]);
            }
        });
    });
}(jQuery));
