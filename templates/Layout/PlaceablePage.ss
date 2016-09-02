<% if CurrentRegions %>
    <div class='regions'>
        <% loop CurrentRegions %>
            {$DebugInfo}
            {$Layout}
        <% end_loop %>
    </div>
<% end_if %>
