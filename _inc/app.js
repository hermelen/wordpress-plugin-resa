jQuery(document).ready(function($){
  if (typeof bookedDays !== 'undefined') {
    console.log(bookedDays);
  };
  if (typeof availableBed !== 'undefined') {
    console.log(availableBed);
  };
  $(function() {
    var eventsArray = [];
    var checkArray = [];
    if (typeof bookedDays !== 'undefined') {
      $.each(bookedDays, function(index, value){
        endDate = new Date(value[1].thedate);
        endDate = endDate.setDate(endDate.getDate() + 1);
        endDate = new Date(endDate);
        displayTitle = value[2]+': réservé';
        eventsArray.push({
          start: new Date(value[0].thedate),
          end: endDate,
          title: displayTitle,
          color: '#550000',
          textColor: 'white'
        })
      });
    }
    if (typeof availableBed !== 'undefined') {
      $.each(availableBed, function(index, value){
        checkValues = value[0]+value[1]+value[2];
        existingRoom = $.inArray(checkValues, checkArray);
        if (existingRoom === -1) {
          day = new Date(value[0]);
          room = value[1];
          bed = value[2];
          var title = room+': '+bed+' lits disp.';
          checkArray.push(checkValues);
          eventsArray.push({
            start: day,
            end: day,
            title: title,
            color: 'teal',
            textColor: 'white'
          })
        }
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
        <table class="table table-dark">
          <thead>
            <tr>
              <th><label for="user_id">Choisir une chambre pour:</label></th>
              <th><label for="room_id">Chambre</label></th>
              <th>Edition</th>
            </tr>
          </thaed>
          <tbody>
            <tr>
              <td>${user_data}<input type="hidden" name="user_id" id="user_id" value="${user_id}"></td>
              <td>
                <select class="form-control" id="room_id" name="room_id"></select>
              </td>
              <td>
                <button class="btn btn-secondary" type="submit" class="save-resa-to-user" id="${user_id}">
                  <i class="far fa-save"></i> Valider
                </button>
              </td>
            </tr>
          </tbody>
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
    var lastname  = $('tr#tr-user-'+user_id+' td:nth-child(1)').html();
    var firstname = $('tr#tr-user-'+user_id+' td:nth-child(2)').html();
    var email     = $('tr#tr-user-'+user_id+' td:nth-child(3)').html();
    var phone     = $('tr#tr-user-'+user_id+' td:nth-child(4)').html();
    $('tr#tr-user-'+user_id).empty();
    $('section.user').prepend(`
      <form class="" action="#" method="post">
        <table class="table">
          <tbody>
            <tr>
              <td>Modification Client <input type="hidden" name="resa_id" value="${user_id}"> </td>
              <td><input type="text" class="form-control" name="resa_lastname" value="${lastname}"></td>
              <td><input type="text" class="form-control" name="resa_firstname" value="${firstname}"></td>
              <td><input type="text" class="form-control" name="resa_email" value="${email}"></td>
              <td><input type="text" class="form-control" name="resa_phone" value="${phone}"></td>
              <td>
                <button type="submit" class="save-user-edition-btn btn btn-secondary">Enreg.modif</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
    `);
  })

  //display user detail
  $('button.info').click(function(e){
    e.preventDefault();
    var userData = JSON.parse($(this).attr('data'));
    $('section.user_detail').empty();
    $('section.user_detail').append(`
      <div class="card" style="width: 18rem;">
        <div class="card-body">
          <h5 class="card-title">${userData[0].toUpperCase()}</h5>
          <h6>${userData[1]}</h6>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><a href="mailto:${userData[2]}">${userData[2]}</a></li>
          <li class="list-group-item">${userData[3]}</li>
        </ul>
        <div class="card-body">
          <a href="#" class="card-link"><button class="btn btn-primary" id="close-detail"><i class="fas fa-times"></i> Fermer</button></a>
        </div>
      </div>
    `)
    $('button#close-detail').click(function(){
      $('section.user_detail').empty();
    })
  })

  // ------------------------DAYS-------------------------------------------------
  // generate dates form on create resa
  $('button.add-date-to-resa').click(function(e){
    e.preventDefault();
    var user_data = $(".pending_user_data").html();
    var room_data = $(".pending_room_data").html();
    $('.date-form').hide();
    $('section.days').empty();
    $('section.days').append(`
      <form action="#" method="post" style="margin-bottom: 3em" class="text-center">
        <h3>${user_data}</h3>
        <h4>${room_data}</h4>
        <table class="table-dark">
          <thead>
            <tr>
              <th><label for="thedate">Jour</label></th>
              <th><label for="dinner">Nbr.Dîner</label></th>
              <th><label for="persons">Nbr.Lit</label></th>
              <th><label for="breakfast">Nbr.Petit-déj(lendemain)</label></th>
              <th><label for="lunch">Nbr.Déj(lendemain)</label></th>
              <th><label for="note">Notes</label></th>
            </tr>
          </thead>
          <tbody id="list-date">
          </tbody>
        </table>
        <button id="submit-all" type="submit" class="btn btn-success">Valider toutes les dates</button>
      </form>`
    );
    var startdate = new Date($('input#start').val());
    var enddate = new Date($('input#end').val());
    var resa_id = $(this).attr('id');
    var dates = getDates(startdate, enddate);
    $.each(dates, function(key, date) {
      var sqlDate = dateYYYYMMDD(date);
      $('tbody#list-date').append(`
        <tr>
          <input name="thedate[${key}]" type="hidden" value="${sqlDate}">
          <input name="resa_id[${key}]" type="hidden" value="${resa_id}"></td>
          <td>${sqlDate}</td>
          <td><input class="form-control" name="dinner[${key}]" type="number"></td>
          <td><input class="form-control" name="persons[${key}]" type="number"></td>
          <td><input class="form-control" name="breakfast[${key}]" type="number"></td>
          <td><input class="form-control" name="lunch[${key}]" type="number"></td>
          <td><input class="form-control" name="note[${key}]" type="text"></td>
        </tr>
      `)
    });
  })

  // generate dates form on edit resa
  $('.add-date-before-after').click(function(e){
    e.preventDefault();
    var container = $(this).closest('tr');
    var user_data = $(container).find("td.user_data").html();
    var room_data = $(container).find('td.room_data').html();
    var resa_id = $(container).find('input.resa_id').val();
    var date = [$(this).prev('input').val()];
    $('section.day').prepend(`
      <form action="#" method="post" style="margin-bottom: 3em" class="text-center">
        <h3>${user_data}</h3>
        <h4>${room_data}</h4>
        <table class="table-dark">
          <thead>
            <tr>
              <th><label for="thedate">Jour</label></th>
              <th><label for="dinner">Nbr.Dîner</label></th>
              <th><label for="persons">Nbr.Lit</label></th>
              <th><label for="breakfast">Nbr.Petit-déj(lendemain)</label></th>
              <th><label for="lunch">Nbr.Déj(lendemain)</label></th>
              <th><label for="note">Notes</label></th>
            </tr>
          </thead>
          <tbody id="list-date">
          </tbody>
        </table>
        <button id="submit-all" type="submit" class="btn btn-success">Valider la nouvelle date</button>
      </form>`
    );
    $('tbody#list-date').append(`
      <tr>
        <input name="thedate[0]" type="hidden" value="${date}">
        <input name="resa_id[0]" type="hidden" value="${resa_id}"></td>
        <td>${date}</td>
        <td><input class="form-control" name="dinner[0]" type="number"></td>
        <td><input class="form-control" name="persons[0]" type="number"></td>
        <td><input class="form-control" name="breakfast[0]" type="number"></td>
        <td><input class="form-control" name="lunch[0]" type="number"></td>
        <td><input class="form-control" name="note[0]" type="text"></td>
      </tr>
    `)
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
    var dinner     = $('tr#tr-day-'+day_id+' td:nth-child(5)').html();
    var persons    = $('tr#tr-day-'+day_id+' td:nth-child(6)').html();
    var breakfast  = $('tr#tr-day-'+day_id+' td:nth-child(7)').html();
    var lunch      = $('tr#tr-day-'+day_id+' td:nth-child(8)').html();
    var note       = $('tr#tr-day-'+day_id+' td:nth-child(9)').html();
    $('tr#tr-day-'+day_id).empty();
    $('tr#tr-day-'+day_id).html(`
      <td></td>
      <input type="hidden" name="id[0]" value="${day_id}">
      <input type="hidden" name="thedate[0]" value="${thedate}">
      <input type="hidden" name="resa_id[0]" value="${resa_id}">
      <td style="color: red">${room_title}</td>
      <td style="color: red">${user}</td>
      <td style="color: red">${thedate}</td>
      <td><input class="form-control" type="text" name="dinner[0]" value="${dinner}"></td>
      <td><input class="form-control" type="text" name="persons[0]" value="${persons}"></td>
      <td><input class="form-control" type="text" name="breakfast[0]" value="${breakfast}"></td>
      <td><input class="form-control" type="text" name="lunch[0]" value="${lunch}"></td>
      <td><textarea name="note[0]" rows="4">${note}</textarea></td>

      <td>
        <button class="icon-btn save-day">
          <i class="far fa-save" style="color: red"></i>
        </button>
      </td>
      <td></td>
      <td></td>
    `);
  })

      // <td><input class="form-control" type="text" name="note[0]" value="${note}"></td>

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

    var totNote = "";
    tdNote = $('.note-'+i);
    tdNote.each(function(){
      if ($(this).html() != '') {
        totNote += $(this).html()+" //";
      };
    })
    $('tr#total-note').append(`
      <td>${totNote}</td>
    `)

  }




  // EXPORT xls
  $("#btnExport").click(function(){
      var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
      var textRange; var j=0;
      tab = document.getElementById('headerTable'); // id of table

      for(j = 0 ; j < tab.rows.length ; j++)
      {
          tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
      }

      tab_text=tab_text+"</table>";

      var ua = window.navigator.userAgent;
      var msie = ua.indexOf("MSIE ");

      if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
      {
          txtArea1.document.open("txt/html","replace");
          txtArea1.document.write(tab_text);
          txtArea1.document.close();
          txtArea1.focus();
          sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
      }
      else                 //other browser not tested on IE 11
          sa = window.open('data:application/vnd.ms-excel, %EF%BB%BF' + encodeURIComponent(tab_text));
      return (sa);
  })

})
