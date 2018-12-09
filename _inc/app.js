jQuery(document).ready(function($){
  console.log("app");

  $('.add-resa-to-user').click(function(){
    var user_id = $(this).attr('id');
    var user_data = $(this).attr('data');
    $('section.resa').append(`
      <form class="resa-form" action="#" method="post" style="margin-bottom: 3em">
        <table>
          <tr>
            <th><h3><label for="user_id">${user_data}</label><h3></th>
            <th><label for="room_id">Chambre</label></th>
            <th>Edition</th>
          </tr>
          <tr>
            <td>${user_data}<input type="hidden" name="user_id" id="user_id" value="${user_id}"></td>
            <td><select id="room_id" name="room_id"></select></td>
            <td><button type="submit" class="save-resa-to-user" id="${user_id}">Enregistrer</button></td>
          </tr>
        </table>
      </form>
    `)
    $.each(selectRoom, function(index, value){
      $('#room_id').append(`
        <option value="${value[0]}">${value[1]}</option>
      `)
    })
  })

  $('.date-form').submit(function(e){
    e.preventDefault();
    $('section.days').empty();
    $('section.days').append(`
      <form action="#" method="post" style="margin-bottom: 3em">
        <table>
          <thead>
            <tr>
              <th><label for="thedate">Jour</label></th>
              <th><label for="persons">Nbr.Lit</label></th>
              <th><label for="breakfast">Nbr.Petit-déj</label></th>
              <th><label for="lunch">Nbr.Déj</label></th>
              <th><label for="dinner">Nbr.Dîner</label></th>
              <label for="resa_id"></label>
            </tr>
          </thead>
          <tbody id="list-date">
          </tbody>
        </table>
        <button id="submit-all" type="submit">Valider toutes les dates</button>
      </form>
    `)
    var startdate = new Date($('input#start').val());
    var enddate = new Date($('input#end').val());
    var resa_id = $('button.add-date-to-resa').attr('id');
    var dates = getDates(startdate, enddate);
    $.each(dates, function(key, date) {
      var sqlDate = formatDate(date);
      $('tbody#list-date').append(`
        <tr>
          <input name="thedate[${key}]" type="hidden" value="${sqlDate}">
          <td>${sqlDate}</td>
          <td><input name="persons[${key}]" type="number"></td>
          <td><input name="breakfast[${key}]" type="number"></td>
          <td><input name="lunch[${key}]" type="number"></td>
          <td><input name="dinner[${key}]" type="number"></td>
          <input name="resa_id[${key}]" type="hidden" value="${resa_id}"></td>
        </tr>
      `)
    });
  })


// Returns an array of dates between the two dates
var getDates = function(startDate, endDate) {
  var dates = [],
      currentDate = startDate,
      addDays = function(days) {
        var date = new Date(this.valueOf());
        date.setDate(date.getDate() + days);
        return date;
      };
  while (currentDate <= endDate) {
    dates.push(currentDate);
    currentDate = addDays.call(currentDate, 1);
  }
  return dates;
};

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}




  // <input type="submit" value="Valider"></button>











































})
