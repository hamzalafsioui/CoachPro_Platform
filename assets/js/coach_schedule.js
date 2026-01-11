document.addEventListener("DOMContentLoaded", function () {
  console.log("Schedule loaded - Version 2.0 (Cache Cleared)");
  const today = new Date();
  fetchReservations(today.getMonth() + 1, today.getFullYear());
});

let events = [];

async function fetchReservations(month, year) {
  try {
    console.log("start");

    const response = await fetch(
      `../../actions/reservations/read.php?month=${month}&year=${year}`
    );
    const result = await response.json();

    if (result.success) {
      console.log(result.success);

      events = result.data.map((res) => ({
        date: res.date,
        title: `${res.client} - ${res.type}`,
        type: res.status.toLowerCase(),
        time: res.time,
        status: res.status,
      }));
      console.log(events);

      renderCalendar();
    }
  } catch (error) {
    console.error("Error fetching reservations:", error);
  }
}

let currentDate = new Date();

function renderCalendar() {
  const calendarGrid = document.querySelector(".calendar-grid");
  const mothYearDisplay = document.getElementById("monthYear");

  if (!calendarGrid || !mothYearDisplay) return;

  // Clear existing day cells
  const dayCells = document.querySelectorAll(".calendar-day");
  
  calendarGrid.innerHTML = ""; 

  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  mothYearDisplay.textContent = `${monthNames[month]} ${year}`;

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  const daysInMonth = lastDay.getDate();
  const startingDay = firstDay.getDay();

  // Previous Month Fillers
  const prevMonthLastDay = new Date(year, month, 0).getDate();
  for (let i = 0; i < startingDay; i++) {
    const dayDiv = document.createElement("div");
    dayDiv.classList.add("calendar-day", "other-month");
    dayDiv.textContent = prevMonthLastDay - startingDay + 1 + i;
    calendarGrid.appendChild(dayDiv);
  }

  // Current Month Days
  for (let i = 1; i <= daysInMonth; i++) {
    const dayDiv = document.createElement("div");
    dayDiv.classList.add("calendar-day");

    // Header for day number
    const dayNum = document.createElement("div");
    dayNum.classList.add("flex", "justify-between", "items-start", "mb-2");

    const spanNum = document.createElement("span");
    spanNum.classList.add("text-sm", "font-semibold", "text-gray-400");
    spanNum.textContent = i;

    // Add button to add event 
    const addBtn = document.createElement("button");
    addBtn.innerHTML = '<i class="fas fa-plus text-xs"></i>';
    addBtn.classList.add(
      "text-gray-600",
      "hover:text-blue-400",
      "transition-colors"
    );
    addBtn.onclick = (e) => {
      e.stopPropagation();
      alert(`Add event for ${monthNames[month]} ${i}, ${year}`);
    };

    dayNum.appendChild(spanNum);
    dayNum.appendChild(addBtn);
    dayDiv.appendChild(dayNum);

    // Check for events
    // Construct date string YYYY-MM-DD
    const currentDayStr = `${year}-${String(month + 1).padStart(
      2,
      "0"
    )}-${String(i).padStart(2, "0")}`;



    const today = new Date();
    if (
      i === today.getDate() &&
      month === today.getMonth() &&
      year === today.getFullYear()
    ) {
      dayDiv.classList.add("today");
    }

    const daysEvents = events.filter((e) => e.date === currentDayStr);

    daysEvents.forEach((evt) => {
      const evtDiv = document.createElement("div");
      evtDiv.classList.add("event-item", `event-${evt.type}`);
      evtDiv.innerHTML = `<span class="font-bold">${evt.time}</span> ${evt.title}`;
      evtDiv.onclick = () =>
        alert(
          `Event Details:\n${evt.title}\nStatus: ${evt.status}\nTime: ${evt.time}`
        );
      dayDiv.appendChild(evtDiv);
    });

    calendarGrid.appendChild(dayDiv);
  }

  // Next Month Fillers
  const totalCells = startingDay + daysInMonth;
  const remainingCells = 42 - totalCells; // 6 rows * 7 columns = 42

  for (let i = 1; i <= remainingCells; i++) {
    const dayDiv = document.createElement("div");
    dayDiv.classList.add("calendar-day", "other-month");
    dayDiv.textContent = i;
    calendarGrid.appendChild(dayDiv);
  }
}

function prevMonth() {
  currentDate.setMonth(currentDate.getMonth() - 1);
  // Fetch for the new month (getMonth() is 0-indexed, so add 1)
  fetchReservations(currentDate.getMonth() + 1, currentDate.getFullYear());
}

function nextMonth() {
  currentDate.setMonth(currentDate.getMonth() + 1);
  fetchReservations(currentDate.getMonth() + 1, currentDate.getFullYear());
}

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar.classList.contains("-translate-x-full")) {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
  } else {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
  }
}

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar.classList.contains("-translate-x-full")) {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
  } else {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
  }
}
