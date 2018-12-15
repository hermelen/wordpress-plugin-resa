jQuery(document).ready(function($){

  $(function() {
    var eventsArray = [];
    if (typeof(bookedDays) !== 'undefined') {
      $.each(bookedDays, function(index, value){
        endDate = new Date(value[1].thedate);
        endDate = endDate.setDate(endDate.getDate() + 1);
        eventsArray.push({
          start: new Date(value[0].thedate),
          end: endDate,
          title: value[2]
        })
      })

    }
      $('#calendar-widget').fullCalendar({
        height: "auto",
        events: eventsArray
      })
      //------------------------DISPLAY RESA PAGE ----------------------------------
      var today = new Date();
      var thisMonth = dateYYYYMM(new Date());
      console.log(thisMonth);

      hideDays(thisMonth);


      $('.fc-prev-button').click(function(){
        today = today.setMonth(today.getMonth() - 1);
        today = new Date(today);
        thisMonth = dateYYYYMM(today);
        hideDays(thisMonth);
      })

      $('.fc-next-button').click(function(){
        today = today.setMonth(today.getMonth() + 1);
        today = new Date(today);
        thisMonth = dateYYYYMM(today);
        hideDays(thisMonth);
      })

      function hideDays(checkedDays){
        $('.td-date').each(function(){
          $(this).closest('tr').show();
          if (!$(this).html().startsWith(thisMonth)) {
            $(this).closest('tr').hide();
          }
        })
      }

  });

  // ------------------------USER-------------------------------------------------
  // add a user
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

  //edit a user
  $('.edit-user').click(function(){
    $('.edit-user').attr('disabled', 'disabled');
    var user_id = $(this).attr('id');
    user_id = user_id.replace("user-", "");
    var lastname  = $('tr#tr-user-'+user_id+' td:nth-child(2)').html();
    var firstname = $('tr#tr-user-'+user_id+' td:nth-child(3)').html();
    var email     = $('tr#tr-user-'+user_id+' td:nth-child(4)').html();
    var phone     = $('tr#tr-user-'+user_id+' td:nth-child(5)').html();
    $('tr#tr-user-'+user_id).empty();
    $('section.user').prepend(`
      <form class="" action="#" method="post">
        <table>
          <tr>
            <td>Modification Client<input type="hidden" name="id" value="${user_id}"></td>
            <td><input type="text" name="lastname" value="${lastname}"></td>
            <td><input type="text" name="firstname" value="${firstname}"></td>
            <td><input type="text" name="email" value="${email}"></td>
            <td><input type="text" name="phone" value="${phone}"></td>
            <td>
              <button type="submit" class="save-user-edition-btn">Enreg.modif</button>
            </td>
            <td></td>
          </tr>
        </table>
      </form>
    `);
  })

  // ------------------------DAYS-------------------------------------------------
  // generate dates form
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
      var sqlDate = dateYYYYMMDD(date);
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

  function dateYYYYMMDD(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();

      if (month.length < 2) month = '0' + month;
      if (day.length < 2) day = '0' + day;

      return [year, month, day].join('-');
  }

  function dateYYYYMM(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          year = d.getFullYear();

      if (month.length < 2) month = '0' + month;

      return [year, month].join('-');
  }




















































})
