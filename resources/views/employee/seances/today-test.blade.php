<!DOCTYPE html>
<html>
<head>
    <title>Test Séances</title>
</head>
<body>
    <h1>Test Séances du jour</h1>
    <p>{{ count($seances) }} séances trouvées</p>

    @foreach($seances as $seance)
        <div>
            <h3>{{ $seance['film_titre'] }}</h3>
            <p>{{ $seance['date_heure_debut']->format('H:i') }}</p>
        </div>
    @endforeach
</body>
</html>