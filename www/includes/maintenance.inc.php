<?php
if (UPDATE_RUNNING == "yes") :
    /**
     *  La page de maintenance s'affiche sur toutes les pages sauf sur /settings
     */
    if (__ACTUAL_URI__ != "/settings") : ?>
        <div id="maintenance-container">    
            <div id="maintenance">
                <h3>UPDATE RUNNING</h3>
                <p>Repomanager will be available soon.</p>
                <br>
                <button class="btn-medium-green" onClick="window.location.reload();">Refresh</button>
            </div>
        </div>
        <?php
    endif;
endif;