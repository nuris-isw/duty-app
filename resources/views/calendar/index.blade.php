<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Kalender Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-4 sm:p-6 border border-neutral-200 dark:border-neutral-700">
                {{-- Kalender akan ditampilkan di sini --}}
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Memuat library FullCalendar --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          locale: 'id', // Bahasa Indonesia
          height: 'auto', // Tinggi kalender menyesuaikan konten
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },
          events: '{{ route("calendar.events") }}',

          // --- PENYESUAIAN TEMA UI/UX ---
          eventColor: '#E4252C', // Warna brand Anda untuk event default
          eventDisplay: 'block',
          
          // Mengatur warna kalender menggunakan CSS Variables
          eventDidMount: function(info) {
            // Memberi warna berbeda jika ada properti 'color' dari data event
            if (info.event.extendedProps.color) {
              info.el.style.backgroundColor = info.event.extendedProps.color;
              info.el.style.borderColor = info.event.extendedProps.color;
            }
          },
          
          // Menyesuaikan tampilan tombol header agar lebih modern
          customButtons: {
              prev: {
                  click: function() { calendar.prev(); },
                  icon: 'chevron-left'
              },
              next: {
                  click: function() { calendar.next(); },
                  icon: 'chevron-right'
              }
          },
          buttonText: {
              today: 'Hari Ini',
              month: 'Bulan',
              week: 'Minggu',
              list: 'Agenda'
          },
          // Menghapus border antar sel untuk tampilan lebih bersih
          dayCellDidMount: function(info) {
              info.el.style.borderColor = 'var(--fc-border-color, #e5e7eb)';
          },
          viewDidMount: function(info) {
              // Menghapus border antar baris di view 'list'
              var listItems = calendarEl.querySelectorAll('.fc-list-event');
              listItems.forEach(function(item) {
                  item.style.borderColor = 'transparent';
              });
          }
        });
        calendar.render();
      });
    </script>
    @endpush
</x-app-layout>