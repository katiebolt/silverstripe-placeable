<main {$ClassAttr}{$AnchorAttr}>
    <% if Title %>
        <h1 class='title'>
            {$Title}
        </h1>
    <% end_if %>
    <% if Content %>
        <div class='content'>
            {$Content}
        </div>
    <% end_if %>
</main>
