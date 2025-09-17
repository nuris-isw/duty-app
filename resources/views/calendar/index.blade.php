<x-app-layout>
    {{-- "Mendorong" CSS FullCalendar ke dalam @stack('styles') di layout --}}
    @push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Kalender Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-2 sm:p-6 border border-neutral-200 dark:border-neutral-700">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        const isMobile = window.innerWidth < 768;

        let calendarConfig = {
          locale: 'id',
          height: 'auto',
          events: '{{ route("calendar.events") }}',
          eventColor: '#E4252C',
          eventDisplay: 'block',
          buttonText: {
              today: 'Hari Ini', month: 'Bulan', week: 'Minggu', list: 'Agenda'
          },
          eventDidMount: function(info) {
            if (info.event.extendedProps.color) {
              info.el.style.backgroundColor = info.event.extendedProps.color;
              info.el.style.borderColor = info.event.extendedProps.color;
            }
          },
        };

        if (isMobile) {
            calendarConfig.initialView = 'listWeek';
            calendarConfig.headerToolbar = {
                left: 'prev,next', center: 'title', right: 'today'
            };
        } else {
            calendarConfig.initialView = 'dayGridMonth';
            calendarConfig.headerToolbar = {
                left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek'
            };
        }
        
        var calendar = new FullCalendar.Calendar(calendarEl, calendarConfig);
        calendar.render();
      });
    </script>
    @endpush
</x-app-layout>