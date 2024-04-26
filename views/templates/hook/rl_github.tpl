<!-- Block github -->
{if isset($github_username) && $github_username}
  <div id="github_block_home" class="block">
    <h4>{l s='Github Link' d='rl_github'}</h4>
    <div class="block_content">
      <a href="https://github.com/{$github_username}">{l s='Link' d='rl_github'}</a>
    </div>
  </div>
{/if}
<!-- /Block github -->