// scripts.js
import { Calendar } from '@fullcalendar/core'
import interactionPlugin from '@fullcalendar/interaction'
import dayGridPlugin from '@fullcalendar/daygrid'

const calendarEl = document.getElementById('calendar')
const calendar = new Calendar(calendarEl, {
  plugins: [
    interactionPlugin,
    dayGridPlugin
  ],
  initialView: 'timeGridWeek',
  editable: true,
  events: [
    { title: 'Meeting', start: new Date() }
  ]
})

calendar.render()


// Exemplo de funcionalidade JS (pode ser expandido conforme necessário)




document.addEventListener('DOMContentLoaded', () => { })

// JavaScript para fechar o modal
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('modalErro');
  var span = document.getElementsByClassName('close')[0];

  // Fechar modal quando clicar no "X"
  span.onclick = function() {
      modal.style.display = 'none';
  }

  // Fechar modal quando clicar fora do conteúdo
  window.onclick = function(event) {
      if (event.target == modal) {
          modal.style.display = 'none';
      }
  }
});
