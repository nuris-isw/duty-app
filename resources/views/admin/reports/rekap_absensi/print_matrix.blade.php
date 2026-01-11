<!DOCTYPE html>
<html>
<head>
    <title>Matriks Absensi</title>
    <style>
        @page { margin: 15px; } /* Margin tipis biar muat banyak */
        body { font-family: sans-serif; font-size: 9px; } /* Font kecil */
        
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 3px 0; font-size: 11px; }

        table { width: 100%; border-collapse: collapse; border-spacing: 0; }
        th, td { border: 0.5px solid #666; padding: 2px; text-align: center; vertical-align: middle; }
        
        th { background-color: #eee; font-weight: bold; height: 20px; }
        .th-date { font-size: 8px; width: 18px; } /* Kolom tanggal kecil */
        .text-left { text-align: left; padding-left: 5px; }

        /* Warna Cell Heatmap */
        .cell { width: 14px; height: 14px; margin: 0 auto; border-radius: 2px; line-height: 14px; font-size: 8px; font-weight: bold; }
        
        .bg-green  { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; } /* Hadir */
        .bg-orange { background-color: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; } /* Telat */
        .bg-yellow { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; } /* Data Kurang */
        .bg-blue   { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; } /* Cuti */
        .bg-red    { background-color: #f43f5e; color: #fff;    border: 1px solid #e11d48; } /* Mangkir */
        .bg-indigo { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; } /* Libur Nasional */
        .bg-gray   { background-color: #f3f4f6; color: #ccc; } /* Weekend */
        .bg-white  { background-color: #fff; }

        .legend { margin-bottom: 10px; font-size: 9px; }
        .legend span { display: inline-block; width: 10px; height: 10px; margin-right: 3px; border: 1px solid #ccc; vertical-align: middle; }
    </style>
</head>
<body>

    <div class="header">
        <h2>MATRIKS ABSENSI PEGAWAI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Legenda Mini --}}
    <div class="legend">
        <span style="background:#d1fae5"></span>Hadir
        <span style="background:#ffedd5; margin-left:8px"></span>Telat
        <span style="background:#fef3c7; margin-left:8px"></span>Kurang
        <span style="background:#e0f2fe; margin-left:8px"></span>Cuti
        <span style="background:#f43f5e; margin-left:8px"></span>Mangkir
        <span style="background:#e0e7ff; margin-left:8px"></span>Libur Nas.
        <span style="background:#f3f4f6; margin-left:8px"></span>Akhir Pekan
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Pegawai</th>
                @foreach($period as $date)
                    <th class="th-date {{ $date->isWeekend() ? 'bg-gray' : '' }}">
                        {{ $date->format('d') }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $user->name }}</td>
                    
                    @foreach($period as $date)
                        @php 
                            $data = $recap[$user->id][$date->format('Y-m-d')];
                            $class = '';
                            $icon = '';
                            
                            switch($data['color']) {
                                case 'green':  $class = 'bg-green';  $icon = '✓'; break;
                                case 'orange': $class = 'bg-orange'; $icon = '!'; break;
                                case 'yellow': $class = 'bg-yellow'; $icon = '?'; break;
                                case 'blue':   $class = 'bg-blue';   $icon = 'i'; break;
                                case 'red':    $class = 'bg-red';    $icon = 'x'; break;
                                case 'holiday':$class = 'bg-indigo'; $icon = '*'; break;
                                case 'gray':   $class = 'bg-gray';   $icon = '-'; break;
                                default:       $class = 'bg-white';
                            }
                        @endphp
                        <td class="{{ $date->isWeekend() ? 'bg-gray' : '' }}">
                            @if($data['color'] !== 'empty' && $data['color'] !== 'gray')
                                <div class="cell {{ $class }}">{{ $icon }}</div>
                            @elseif($data['color'] === 'gray')
                                <span style="color:#ccc">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>