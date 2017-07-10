<form action="{$action}" enctype="application/x-www-form-urlencoded" class="form-horizontal hipay-form-17" method="post"
      name="tokenizerForm" id="tokenizerForm" autocomplete="off">
    {if $confHipay.payment.global.card_token}
        {if $savedCC}
            <p><strong>{l s='Pay with a saved credit or debit card' mod='hipay_enterprise'}</strong></p>
            <div id="error-js-oc" style="display:none" class="alert alert-danger">
                <p>There is 1 error</p>
                <ol>
                    <li class="error-oc"></li>
                </ol>
            </div>
            {foreach $savedCC as $cc}
                <label>
                    <span class="custom-radio">
                        <input type="radio" name="ccTokenHipay" value="{$cc.token}"/>
                        <span></span>
                    </span>
                    {$cc.pan} <img src="{$this_path_ssl}/views/img/{$cc.brand|lower}_small.png"/> <br/>
                    {l s='Expiration date'} : {"%02d"|sprintf:$cc.card_expiry_month} / {$cc.card_expiry_year}<br/>
                    {$cc.card_holder}
                </label>
                <br/>
            {/foreach}
        {/if}
    {/if}
    <p><strong>{l s='Pay by credit or debit card' mod='hipay_enterprise'}</strong></p>
    <div id="error-js" style="display:none" class="alert alert-danger">
        <p>There is 1 error</p>
        <ol>
            <li class="error"></li>
        </ol>
    </div>
    {include file="$hipay_enterprise_tpl_dir/paymentForm.tpl"}
    <br/>
    <span class="custom-checkbox">
        <input type="checkbox" name="saveTokenHipay" checked>
        <span><i class="material-icons checkbox-checked"></i></span>
        <label>{l s='Save credit card (One click payment)' mod='hipay_enterprise'}</label>
    </span>
</form>
<p id="payment-loader-hp" style='text-align: center; display:none;'>
    <strong>{l s='Your payment is being processed. Please wait.'}</strong> <br/>
    <img src="{$this_path_ssl}/views/img/loading.gif">
</p>
<script>

    {if $confHipay.account.global.sandbox_mode}
    var api_tokenjs_mode = 'stage';
    var api_tokenjs_username = '{$confHipay.account.sandbox.api_tokenjs_username_sandbox}';
    var api_tokenjs_password_publickey = '{$confHipay.account.sandbox.api_tokenjs_password_publickey_sandbox}';
    {else}
    var api_tokenjs_mode = 'production';
    var api_tokenjs_username = '{$confHipay.account.production.api_tokenjs_username_production}';
    var api_tokenjs_password_publickey = '{$confHipay.account.production.api_tokenjs_password_publickey_production}';
    {/if}
</script>