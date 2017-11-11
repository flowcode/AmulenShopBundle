/**
 * Created by juanma on 4/23/16.
 */



var $collectionHolder;
var $addTagLink = $(tagAAddOther);
jQuery(document).ready(function () {
    $collectionHolder = $('#contentMaterials');
    $collectionHolder.append($addTagLink);
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    $collectionHolder.find('.raw-materials').each(function () {
        addReminderFormDeleteLink($(this));
    });
    $addTagLink.on('click', function (e) {
        e.preventDefault();
        addReminderForm($collectionHolder, $addTagLink);
    });
});
function addReminderForm($collectionHolder, $newLink) {
    var prototype = $("#productRawMaterialTemplate");
    var index = $collectionHolder.data('index');

    prototype.find('.field').each(function () {
        var field = $(this);
        field.append(field.data('prototype'));
    });

    var newForm = prototype.html().replace(/__name__/g, index);
    newForm = $(newForm);
    $collectionHolder.data('index', index + 1);
    $newLink.before(newForm);
    addReminderFormDeleteLink(newForm);

    prototype.find('.field').each(function () {
        $(this).empty();
    });
    $('.select2entity[data-autostart="true"]').select2entity();
    styleOnSelect2(true);
}

function addReminderFormDeleteLink($tagFormLi) {
    var $removeFormA = $(tagARemove);
    $tagFormLi.find(".tools").append($removeFormA);
    $removeFormA.on('click', function (e) {
        e.preventDefault();
        $tagFormLi.remove();
    });
}

function styleOnSelect2(lastElement) {
    $.fn.select2entityAjax = function (action) {
        var action = action || {};
        var template = function (item) {
            var img = item.img || null;
            if (!img) {
                if (item.element && item.element.dataset.img) {
                    img = item.element.dataset.img;
                } else {
                    return item.text;
                }
            }
            return $(
                '<p><img src="' + img + '" class="img-circle img-sm" style="margin-bottom: 2px;"> <span style="margin-left: 10px;">' + item.text + '</span></p>'
            );
        };
        this.select2entity($.extend(action, {
            templateResult: template,
            templateSelection: template
        }));
        return this;
    };
    if(lastElement){
        $('.select2entity').last().select2entityAjax();
    } else {
        $('.select2entity').select2entityAjax();
    }
}