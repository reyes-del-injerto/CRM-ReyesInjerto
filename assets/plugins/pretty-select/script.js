let ShowStatus = true;

$(".dropdown-el").click(function (e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).toggleClass("expanded");

  const target = $(e.target).attr("for");
  const target_val = $(e.target).data("value");
  const event_type = $(e.target).data("event-type");

  alert(event_type);

  $("#" + target).prop("checked", true);
  $(`#${event_type}_stage`).val(target_val);
});
$(document).on("click", ".dropdown-el", function () {
  $(".dropdown-el").removeClass("expanded");
});
