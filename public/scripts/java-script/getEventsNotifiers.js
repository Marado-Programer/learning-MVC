var times;
var ajax;

window.onload = function() {
	if (document.getElementById("idOfUser").value > 0) ajax();
}

function ajax() {
	try {
		ajax = new XMLHttpRequest();
	} catch(e) {
		try {
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {
				console.log(e.toString());
				return false;
			}
		}
	}

	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4 && ajax.status == 200) {
			try {
				setTimers(JSON.parse(this.responseText));
			} catch(e) {
				console.log(e.toString());
			}
		}
	}

	try {
		ajax.open("GET", "http://localhost/learning-MVC/src/getEvents.php?userID=" + document.getElementById("idOfUser").value, true);
	} catch(e) {
		console.log(e.toString());
	}

	try {
		ajax.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		ajax.send(null);
	} catch(e) {
		console.log(e.toString());
	}
}

function createNotification(message) {
	var notification = document.createElement("div");
	notification.appendChild(document.createTextNode(message));

    var button = document.createElement("button");
	button.appendChild(document.createTextNode("X"));

    button.onclick = () => { button.parentNode.remove() };

	notification.appendChild(button);

	if (!document.body.contains(document.getElementById("notifications"))) {
		var notifDiv = document.createElement("div");
		notifDiv.setAttribute("id", "notifications");
        notifDiv.style.position = 'sticky';
        notifDiv.style.top = '0';
        notifDiv.style.right = '0';
        notifDiv.style.backgroundColor = '#F0027F';

		document.body.insertBefore(notifDiv, document.body.firstChild);
	}

	document.getElementById("notifications").appendChild(notification);
}

function setTimers(events) {
	var now = new Date();

	var anHour = 1000 * 60 * 60;
	var aDay = anHour * 24;
	var aWeek = aDay * 7;
	var aMonth = aDay * 30;

	events.forEach( (event) => {
		var date = new Date(event.dateString);

		if (now.getTime() + aDay >= date.getTime()) {
			createNotification("Less than a day until the " + event.title + " event --- " + event.description);
            for (var i = 24; (now.getTime() + anHour * i) >= date.getTime(); i--)
                setTimeout(() => createNotification("Less than " + i + " hour(s) until the " + event.title + " event --- " + event.description), date.getTime - (now.getTime + anHour * i));
			return;
		}
		if ((now.getTime() + aWeek) >= date.getTime()) {
			createNotification("Less than a week until the " + event.title + " event --- " + event.description);
            for (var i = 7; (now.getTime() + aDay * i) >= date.getTime(); i--)
                setTimeout(() => createNotification("Less than " + i + " days(s) until the " + event.title + " event --- " + event.description), date.getTime - (now.getTime + aDay * i));
			return;
		}
		if ((now.getTime() + aMonth) >= date.getTime()) {
			createNotification("Less than a month until the " + event.title + " event --- " + event.description);
            for (var i = 5; (now.getTime() + aWeek * i) >= date.getTime(); i--)
                setTimeout(() => createNotification("Less than " + i + " weeks(s) until the " + event.title + " event --- " + event.description), date.getTime - (now.getTime + aWeek * i));
			return;
		}
        for (var i = 12; (now.getTime() + aMonth * i) >= date.getTime(); i--)
            setTimeout(() => createNotification("Less than " + i + " month(s) until the " + event.title + " event --- " + event.description), date.getTime - (now.getTime + aMonth * i));
	});
}
