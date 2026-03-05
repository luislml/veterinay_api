<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se Busca - {{ $pet->name }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
        }

        .container {
            width: 90%;
            height: 100vh;
            /* Use vh to fill page */
            padding: 40px;
            /* Outer white margin */
            box-sizing: border-box;
        }

        .border-red {
            
            /* Thick red border */
            height: 95%;
            /* Fill almost all the container height */
            display: flex;
            flex-direction: column;
            position: relative;
            background: #fff;
            padding: 0;

        }

        .photo-area {
            width: 100%;
            height: 40%; /* altura que quieras para la sección de la foto */
            background-color: #f0f0f0;
            text-align: center;
            overflow: hidden;
            background-color: transparent;
            position: relative;
            border-bottom: 5px solid #c0392b;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-area img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* mantiene proporción original */
        }

        .text-content {
            text-align: center;
            padding: 10px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header-red {
            background-color: #c0392b;
            color: white;
            padding: 10px 0;
            font-size: 85px;
            /* Huge text */
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            margin: 0;
            letter-spacing: 2px;
            text-align: center;
        }

        .sub-header {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 5px 0;
            color: #000;
        }

        .pet-name-container {
            font-size: 70px;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            display: block;
            line-height: 1.2;
            color: #000;
        }

        .paw-icon {
            font-size: 40px;
            vertical-align: middle;
            color: #000;
        }

        .details-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: separate;
            border-spacing: 20px 0;
        }

        .detail-col {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }

        .detail-label {
            font-weight: bold;
            font-size: 14px;
            /* Matches standard small print */
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }

        .detail-label i {
            margin-right: 5px;
        }

        .detail-text {
            font-size: 16px;
            line-height: 1.4;
            color: #333;
        }

        .phone-section {
            margin-top: 20px;
            color: #c0392b;
            font-size: 50px;
            font-weight: bold;
            display: block;
        }

        /* ---------- Tiras de tickets verticales CORREGIDAS ---------- */
        .strips-area {
            height: 200px;
            width: 100%;
            border-top: 2px dashed #000;
            margin-top: auto;
        }

        .strips-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .strip-cell {
            width: calc(100% / 10);
            height: 150px;
            border-right: 1px dashed #000;
            position: relative;
            padding: 0;
        }

        .strip-cell:last-child {
            border-right: none;
        }

        .strip-cell .strip-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            white-space: nowrap;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            line-height: 1.1;
        }

        .strip-content .phone-strip {
            color: #cc0000;
            font-size: 12px;
            font-weight: 900;
            display: block;
            margin-bottom: 2px;
        }

        .strip-content .name-strip {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="border-red">
            <!-- Photo Section -->
            <div class="photo-area">
                @if($pet->images->count() > 0)
                                <?php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $image = $pet->images->first();
                    // Construct path assuming storage/files structure from HasFiles trait
                    $imagePath = public_path('storage/files/' . $image->name . '.' . $image->extension);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ?>
                                <img src="{{ $imagePath }}" alt="{{ $pet->name }}">
                @else
                    <div
                        style="display:flex; align-items:center; justify-content:center; height:100%; color: #999; font-size: 30px;">
                        [FOTO]
                    </div>
                @endif
            </div>

            <!-- Text Content -->
            <!-- Red Header Band inside the border structure or just below photo? -->
            <!-- In the reference image, "SE BUSCA" is a red band. -->
            <div class="header-red">SE BUSCA</div>

            <div class="text-content">
                <div class="sub-header">AYÚDAME A REGRESAR A CASA</div>

                <div class="pet-name-container" style="text-align:center; font-size:48px; font-weight:900; margin:12px 0;">
                    <img src="{{ public_path('storage/files/paw-black.png') }}" 
                        style="width:32px; height:32px; vertical-align:middle; margin-right:8px;" 
                        alt="patita">
                    {{ $pet->name }}
                    <img src="{{ public_path('storage/files/paw-black.png') }}" 
                        style="width:32px; height:32px; vertical-align:middle; margin-left:8px;" 
                        alt="patita">
                </div>
                <table class="details-table">
                    <tr>
                        <td class="detail-col">
                            <span class="detail-label"> FECHA Y LUGAR DE EXTRAVIO</span>
                            <div class="detail-text">
                                {{ \Carbon\Carbon::parse($ad->date)->isoFormat('D [de] MMMM YYYY') }}<br>
                                {{ $ad->description }}
                            </div>
                        </td>
                        <!-- <td class="detail-col">
                            <span class="detail-label">🏷️ SEÑAS PARTICULARES:</span>
                            <div class="detail-text">
                                {{ $pet->race->name ?? 'Raza desconocida' }},
                                {{ $pet->color ?? 'Color desconocido' }}<br>
                                {{ \Carbon\Carbon::parse($pet->birthday)->age }} años, {{ $pet->gender ?? '' }}.
                            </div>
                        </td> -->
                    </tr>
                </table>

                <div class="phone-section">
                    tel. {{ $pet->client->phone ?? '(00) 0000-0000' }}
                </div>
                <br>
                <div class="strips-area">
                    <table class="strips-table">
                        <tr>
                            @for($i = 0; $i < 10; $i++)
                                <td class="strip-cell">
                                    <div class="strip-content">
                                        <span class="name-strip">
                                            {{ strtoupper(\Illuminate\Support\Str::limit($pet->client->name . ' ' . $pet->client->last_name, 15)) }}
                                        </span>
                                        <span class="phone-strip">phone {{ $pet->client->phone }}</span>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>