<% if CurrentBlocks %>
    <div {$ClassAttr}{$AnchorAttr}>
        <% loop CurrentBlocks %>
            {$DebugInfo}
            {$Layout}
        <% end_loop %>
    </div>
<% end_if %>
