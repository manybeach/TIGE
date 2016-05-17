/**
 * Created by Mathieu on 14/05/2016.
 */
function affichageLignes(jeuaffiche, jeumasque) {
    var i;
    if(jeuaffiche!=null && jeumasque!=null ) {
        for (i = 0; i <= 10; i++ ) {
            if (document.getElementById(jeuaffiche + i) != null)
                document.getElementById(jeuaffiche + i).style.display = 'block';

            if (document.getElementById(jeumasque + i) != null)
                document.getElementById(jeumasque + i).style.display = 'none';
        }
    }
    else {
        for (i = 0; i <= 10; i++) {
            if (document.getElementById('Hots' + i) != null)
                document.getElementById('Hots' + i).style.display = 'block';
            if (document.getElementById('LoL' + i) != null)
                document.getElementById('LoL' + i).style.display = 'block';
        }
    }
}
