[{$smarty.block.parent}]

<tr>
    <td class="edittext" style="padding: 20px 0;" valign="top">
        <b>Zustelldatum</b><br>tabslDhlTracking
    </td>
    <td class="edittext" style="padding: 20px 0; line-height: 1.8;" valign="top">
        [{if $edit->oxorder__oxtrackcode->value}]
            <input type="text" class="editinput" size="20" maxlength="[{$edit->oxorder__tabsldhltracking_deliverydate->fldmax_length}]" name="editval[oxorder__tabsldhltracking_deliverydate]" value="[{$edit->oxorder__tabsldhltracking_deliverydate->value}]" [{ $readonly }]>
            &nbsp; <a href="[{ $oViewConf->getSelfLink() }]cl=order_main&fnc=updateTabslDhl&oxid=[{ $edit->oxorder__oxid->value }]">aktualisieren</a>
            <br><br>
            [{if $edit->oxorder__tabsldhltracking_info->value}]
                [{assign var="status" value=$edit->getTabslDhlStatus()}]
                <b>Status: <span style="color: [{if $status.statusCode == "failure"}]red[{else}]green[{/if}];">[{$status.description}]</b><br>
                <a href="javascript:void(0);" onclick="toggleVisibility('dhlEvents')">&rarr; Ereignisse anzeigen</a>
                <div id="dhlEvents" style="display: none;">
                    [{assign var="events" value=$edit->getTabslDhlEvents()}]
                    [{if $events}]
                        [{foreach name=events from=$events item=event}]
                            [{$event.timestamp|date_format:"%d.%m.%Y %H:%M"}] - [{$event.description}]<br>
                        [{/foreach}]
                        <br>
                    [{/if}]
                </div>
                <br>
                <a href="javascript:void(0);" onclick="toggleVisibility('dhlInfo')">&rarr; Alle Informationen anzeigen</a>
                <div id="dhlInfo" style="display: none;">
                    [{$edit->getTabslDhlInfo()}]
                </div>
                <br>
            [{/if}]
            <a href="https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=[{$edit->oxorder__oxtrackcode->value}]&recipientPostalCode=[{if $edit->oxorder__oxdelzip->value}][{$edit->oxorder__oxdelzip->value}][{else}][{$edit->oxorder__oxbillzip->value}][{/if}]" target="_blank">&rarr; DHL Sendungsverfolgung</a><br>
        [{else}]
            <i>Kein Tracking Code hinterlegt</i>
        [{/if}]
    </td>
</tr>

<script>
    function toggleVisibility(id) {
        const element = document.getElementById(id);
        if (element.style.display === "none") {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }
    }
</script>
