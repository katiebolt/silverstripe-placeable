<% if Placements %>
    <div class='regions'>
        <% loop Placements %>
            {$DebugInfo}
            {$Layout}
        <% end_loop %>
    </div>
<% end_if %>
