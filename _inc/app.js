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

      hideDays(thisMonth);

      $('.fc-today-button').click(function(){
        today = new Date();
        thisMonth = dateYYYYMM(new Date());
        hideDays(thisMonth);
      })

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
            <th><h3><label for="user_id">Choisir une chambre pour:</label><h3></th>
            <th><label for="room_id">Chambre</label></th>
            <th>Edition</th>
          </tr>
          <tr>
            <td>${user_data}<input type="hidden" name="user_id" id="user_id" value="${user_id}"></td>
            <td><select id="room_id" name="room_id"></select></td>
            <td><button type="submit" class="save-resa-to-user" id="${user_id}"><i class="far fa-save"> Valider</button></td>
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
          </tr>
        </table>
      </form>
    `);
  })

  //display user detail
  $('button.info').click(function(){
    var userData = JSON.parse($(this).attr('data'));
    $('section.user_detail').empty();
    $('section.user_detail').append(`
        <h5>Nom: <span>${userData[0]}</h5>
        <h5>Prénom: <span>${userData[1]}</h5>
        <h5>E-mail: <a href="mailto:${userData[2]}">${userData[2]}</a></h5>
        <h5>Téléphone: <span>${userData[3]}</h5>
    `)
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
          <input name="resa_id[${key}]" type="hidden" value="${resa_id}"></td>
          <td>${sqlDate}</td>
          <td><input name="persons[${key}]" type="number"></td>
          <td><input name="breakfast[${key}]" type="number"></td>
          <td><input name="lunch[${key}]" type="number"></td>
          <td><input name="dinner[${key}]" type="number"></td>
        </tr>
      `)
    });
  })

  //edit a day
  $('.edit-day').click(function(){
    $('.edit-day').attr('disabled', 'disabled');
    var day_id = $(this).attr('id');
    var resa_id = $('button.edit-day').attr('resa_id');
    day_id = day_id.replace("day-", "");
    var room_title = $('tr#tr-day-'+day_id+' td:nth-child(2)').html();
    var user       = $('tr#tr-day-'+day_id+' td:nth-child(3)').html();
    var thedate    = $('tr#tr-day-'+day_id+' td:nth-child(4)').html();
    var persons    = $('tr#tr-day-'+day_id+' td:nth-child(5)').html();
    var breakfast  = $('tr#tr-day-'+day_id+' td:nth-child(6)').html();
    var lunch      = $('tr#tr-day-'+day_id+' td:nth-child(7)').html();
    var dinner     = $('tr#tr-day-'+day_id+' td:nth-child(8)').html();
    $('tr#tr-day-'+day_id).empty();
    $('section.edit').prepend(`
      <form class="edit-day-form" action="#" method="post">
        <table style="border: 1px solid red">
          <tr>
            <th>Chambre</th>
            <th>Client</th>
            <th>Date</th>
            <th>Personnes</th>
            <th>Petit-déj</th>
            <th>Déj</th>
            <th>Dîner</th>
            <th>Edition</th>
          </tr>
          <tr>
            <input type="hidden" name="id[0]" value="${day_id}">
            <input type="hidden" name="thedate[0]" value="${thedate}">
            <input type="hidden" name="resa_id[0]" value="${resa_id}">
            <td>${room_title}</td>
            <td>${user}</td>
            <td>${thedate}</td>
            <td><input type="text" name="persons[0]" value="${persons}"></td>
            <td><input type="text" name="breakfast[0]" value="${breakfast}"></td>
            <td><input type="text" name="lunch[0]" value="${lunch}"></td>
            <td><input type="text" name="dinner[0]" value="${dinner}"></td>
            <td>
              <button type="submit" class="save-day-edition-btn">Enreg.modif</button>
            </td>
          </tr>
        </table>
      </form>
    `);
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


  // Excel export DISPLAY

  var number = $('th').length;
  number -= 2;

  for (var i = 0; i < number; i++) {
    var totPersons = 0;
    tdPersons = $('.persons-'+i);
    tdPersons.each(function(){
      if ($(this).html() != '') {
        totPersons += parseInt($(this).html());
      };
    })
    if ( totPersons == 0 ) { totPersons = "" }
    $('tr#total-persons').append(`
      <td>${totPersons}</td>
    `)

    var totBreakfast = 0;
    tdBreakfast = $('.breakfast-'+i);
    tdBreakfast.each(function(){
      if ($(this).html() != '') {
        totBreakfast += parseInt($(this).html());
      };
    })
    if ( totBreakfast == 0 ) { totBreakfast = "" }
    $('tr#total-breakfast').append(`
      <td>${totBreakfast}</td>
    `)

    var totLunch = 0;
    tdLunch = $('.lunch-'+i);
    tdLunch.each(function(){
      if ($(this).html() != '') {
        totLunch += parseInt($(this).html());
      };
    })
    if ( totLunch == 0 ) { totLunch = "" }
    $('tr#total-lunch').append(`
      <td>${totLunch}</td>
    `)

    var totDinner = 0;
    tdDinner = $('.dinner-'+i);
    tdDinner.each(function(){
      if ($(this).html() != '') {
        totDinner += parseInt($(this).html());
      };
    })
    if ( totDinner == 0 ) { totDinner = "" }
    $('tr#total-dinner').append(`
      <td>${totDinner}</td>
    `)
  }




















































})
