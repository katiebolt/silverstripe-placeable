<% if Blocks %>
    <div {$ClassAttr}{$AnchorAttr}>
        <% loop Blocks %>
            {$DebugInfo}
            {$Layout}
        <% end_loop %>
    </div>
<% end_if %>
