/**
 * Created by jfsenechal on 28/12/16.
 */

// setup an "add a tag" link
var $addTagLink = $('#add-another-accompagnateur');
var $newLinkLi = '<li class="list-group-item"></li>';
jQuery(document).ready(function () {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('#accompagnateurs-list');

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function() {
        addTagFormDeleteLink($(this));
    });

    $addTagLink.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();
        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi);
    });
});

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li  class="list-group-item"></li>').append(newForm);
    // also add a remove button, just for this example
    $newFormLi.append('<a href="#"><i class="fas fa-trash"></i> </a>');
    $collectionHolder.append($newFormLi);

    // handle the removal, just for this example
   $('.remove-tag').click(function (e) {
        e.preventDefault();
        $(this).parent().remove();
        return false;
    });
}

function addTagFormDeleteLink($tagFormLi) {

    var $removeFormA = $('<a href="#"><i class="fas fa-trash"></i> </a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $tagFormLi.remove();
    });
}
