<section id="policy" class="block_content">
    <header id="policy_head">
        <h1 class="block_content_title">
            <a href="<?php echo Route::buildUrl('policy'); ?>">
                <?php echo $o->trad('title') ? $o->hscTrad('title') : AcidRouter::getName('policy'); ?>
            </a>
        </h1>
    </header>
    <div id="page_content" class="content_body">
        
        <?php if ($o->trad('content')) { ?>
            <div id="policy_main_content">
                <?php echo $o->trad('content'); ?>
            </div>
        <?php } ?>
        <?php if ($categories = Lib::getIn('elts', $v)) { ?>
            <form method="POST" id="policy_admin">
                <div>
                    <input type="hidden" name="module_do" value="<?php echo ScriptCategory::getClass(); ?>" />
                    <input type="hidden" name="<?php echo ScriptCategory::preKey('do');  ?>" value="policy" />
                    
                    <?php foreach ($categories as $category) { ?>
                        <?php if ($category->get('show')) { ?>

                            <h2><?php echo $category->hscTrad('name'); ?></h2>
                            <div>
                                <?php echo $category->trad('description'); ?>
                            </div>
                            <?php if ($category->get('use_cookie')) { ?>
                                <div>
                                    <label>
                                        <input type="radio" value="accept"
                                               name="<?php echo $category->cookiename(); ?>"
                                            <?php
                                            echo $category->hasConsent() ?
                                                'checked="checked"' :'';
                                            ?>
                                        />
                                        <?php echo Acid::trad('consent_accept'); ?>
                                    </label>
                                    <label>
                                        <input type="radio" value="deny"
                                               name="<?php echo $category->cookiename(); ?>"
                                            <?php
                                            echo !$category->hasConsent() ?
                                                'checked="checked"' :'';
                                            ?>
                                        />
                                        <?php echo Acid::trad('consent_deny'); ?>
                                    </label>
                                </div>
                            <?php } ?>
                            <?php if ($scripts = $category->scripts()) { ?>

                                <ul>
                                    <?php foreach ($scripts as $script) { ?>
                                        <?php if ($script->get('show')) { ?>
                                            <li>
                                                <h2><?php echo $script->hscTrad('name'); ?></h2>
                                                <div>
                                                    <?php echo $script->trad('description'); ?>
                                                </div>
                                                <?php if ($script->get('optional')) { ?>
                                                    <div>
                                                        <label>
                                                            <input type="radio" value="accept"
                                                                   name="<?php echo $script->cookiename(); ?>"
                                                                <?php
                                                                    echo $script->hasConsent() ?
                                                                    'checked="checked"' :'';
                                                                ?>
                                                            />
                                                            <?php echo Acid::trad('consent_accept'); ?>
                                                        </label>
                                                        <label>
                                                            <input type="radio" value="deny"
                                                                   name="<?php echo $script->cookiename(); ?>"
                                                                <?php
                                                                echo !$script->hasConsent() ?
                                                                    'checked="checked"' :'';
                                                                ?>
                                                            />
                                                            <?php echo Acid::trad('consent_deny'); ?>
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        
                        <?php } ?>
                    <?php } ?>

                    <div>
                        <input type="submit" class="btn" value="<?php echo Acid::trad('consent_action_send');  ?>" />
                    </div>
                </div>
            </form>
        <?php } ?>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</section>
