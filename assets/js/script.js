function toggleDetails(eventId) {
  const details = document.getElementById(`details-${eventId}`);
  if (details.style.display === "none" || details.style.display === "") {
    details.style.display = "block";
  } else {
    details.style.display = "none";
  }
}
