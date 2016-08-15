/**
* File: CMSMain.RegionForm.js
*/
(function($) {
    $.entwine('ss', function($){
        $(".cms-add-form #Form_AddForm_PlaceablePageTypeID li").entwine({
            onclick: function(e) {
                this.setSelected(true);
                $(".cms-add-form #PageType li")
                    .toggleClass('selected', false)
                    .children().prop('checked', false);
                $(".cms-add-form input[name='PageType']")
                    .filter('[value=PlaceablePage]').prop('checked', true);
            },
            setSelected: function(bool) {
                var input = this.find('input');
                if(bool && !input.is(':disabled')) {
                    this.siblings().setSelected(false);
                    this.toggleClass('selected', true);
                    input.prop('checked', true);
                } else {
                    this.toggleClass('selected', false);
                    input.prop('checked', false);
                }
            },
        });
        $(".cms-add-form #Form_AddForm_PageType").entwine({
            onclick: function(e) {
                $(".cms-add-form #PlaceablePageTypeID li")
                    .toggleClass('selected', false)
                    .children().prop('checked', false);
            }
        });

        // Edit settings

        $(".cms-edit-form #Form_EditForm_PageType").entwine({
            onmatch: function(e) {
                this.val($('#Form_EditForm_ClassName').val()+'-'+$('#Form_EditForm_PageTypeID').val());
            },
            onchange: function(e) {
                values = this.chosen().val().split('-');
                $('#Form_EditForm_ClassName').val(values[0]);
                $('#Form_EditForm_PageTypeID').val(values[1]);
            }
        });

    });
}(jQuery));
