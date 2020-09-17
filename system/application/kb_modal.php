<?php
require_once SYSTEMPATH .'body.php';
require_once __DIR__ .'/kb_common.php';
?>

<style>
.kb-item {
	font-size:16px;
}
.kb-item hr {
    margin-top: 10px;
    margin-bottom: 10px;
}
.kb-item>a.collapsed {
    display: block;
    padding: 0 0 1rem;
    margin: 0 0 1.2rem;
    border-bottom: 1px solid lightgray;
}
.kb-item-content {
    margin-bottom: 1rem;
    border-bottom: 1px solid lightgray;
}
.kb-item-content>*:last-child {
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}
#btn-objections {
	margin: 0 0.5rem;
}
#btn-objections > span:before,
#btn-definitions > span:before {
    font: inherit;
    content: " Show ";
}
#btn-objections.open > span:before,
#btn-definitions.open > span:before {
    font: inherit;
    content: " Hide ";
}
.btn-add-objection {
    margin: 0.5rem;
}

.sidebar .kb-item * {
	font-size: 12px;
    text-align: left;
    line-height: 1.6em;
}
.sidebar .kb-item > h4,
.sidebar .kb-item-content > p {
    padding: 0 0.5em 0 0;
}
.sidebar .kb-item>h4 {
    font-weight: 600;
    margin: 0 auto 0.3rem;
    padding: 0 0.5em 0 0;
}

#kb-modal {
	z-index: 9999; position: absolute;
}
#kb-modal .modal-body {
    padding: 0.5rem 2rem;
}
#placeholder_kb_modal_content {
	overflow: hidden;
}
</style>

<div id="kb-modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding:10px">
                <h5 class="modal-title text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 36px;">&times;</span>
                    </button>
                </h5>
            </div>

            <div class="modal-body" id="placeholder_kb_modal_content"> </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div><!-- kb-modal -->
<script type="text/javascript">
    function insertTemplate( target, text ) {
        const $target = $(target)
        console.assert($target.length, "ERROR: can't find the specified target!")

        const _text = trim( $target.val() )

        $target.val( trim( _text + " " + text ) )
    }
    function insertTemplateHere( text ) {
        const target = globalThis['focused_kb_target']
        if( !target ) {
            console.log( `target not specified!!`, {text} ); return;
        }
        insertTemplate( target, text )
    }
    function _glowTarget( event = "mouseleave", selector = 'textarea.glowing' ) {
        if( event == "mouseleave" ) {
            const $old = $(selector)
            if( $old.length ) {
                $old.removeClass('glowing')
                //console.log( $old.id, "unglowed" )
            }
        } else {
            const target = globalThis['focused_kb_target'];
            if( target ) { //debugger;
                const $target = $(target);
                if( $target.length ) {
                    $target.addClass('glowing')
                    $target[0].scrollIntoView({behavior: "auto", block: "center", inline: "nearest"})
                    //console.log($target.attr('id'), "glowed")
                }
            }
        }
    }

    DefinitionPanel = {
        dockSide: `<?= DOCK_SIDE ?>`,
        area_id: <?= KB_AREA_DEFINITIONS ?>,
        toggler: `#btn-definitions`,
        actions: `.btn-add-definition`,
        targets: `textarea[name*="question_titles["]`,
    }

    ObjectionPanel = {
        dockSide: `<?= DOCK_SIDE ?>`,
        area_id: <?= KB_AREA_OBJECTION_TEMPLATES ?>,
        toggler: `#btn-objections`,
        actions: `.btn-add-objection`,
        targets: `textarea[name*="objection["]`,
    }

    function toggleKBSidebar( form_id, panel ) { debugger;
        console.assert( panel && panel.dockSide, 'This needs a SomePanel literal, see examples above...' )
        const $sidebar = $(`.sidebar.${panel.dockSide}`).toggleClass("open"),
            willOpen = $sidebar.hasClass("open")
        $(panel.toggler).toggleClass( "open", willOpen )
        if( willOpen ) {
            $.post( `<?= ROOTURL ?>system/application/kb.php?area=${panel.area_id}&section_filter=${form_id}` )
                .done( data => {
                    $sidebar_items = $(`.sidebar.${panel.dockSide} > .fixed>*`)
                    if( $sidebar_items.length < 1 || $sidebar_items.data('section') != form_id ) {
                        $(`.sidebar.${panel.dockSide} > .fixed`).html(data).data('section', form_id)

                        $(panel.actions)
                            .on('mouseenter', ev => _glowTarget( 'mouseenter' ) )
                            .on('mouseleave', ev => _glowTarget( 'mouseleave', `${panel.targets}.glowing` ) )
                    }
                    const $textboxes = $(panel.targets)
                    globalThis['focused_kb_target'] = $textboxes.first()
                    $textboxes.on('focus', ev => {
                        const _target = `#${ev.target.id}`
                        globalThis['focused_kb_target'] = _target;
                        console.log(`target = ${_target}`)
                    } )
                } )
        }
    }
    function showKB( area_id, section_filter="", target="" ) {
        console.assert( area_id != <?= KB_AREA_OBJECTION_TEMPLATES ?>, `ERROR: wrong area_id value here`)
        $.post( `<?= ROOTURL ?>system/application/kb.php?area=${area_id}&section_filter=${section_filter}`, {target} )
            .done( data => {
                $("#placeholder_kb_modal_content").html(data);
                $('#kb-modal').modal('show');
            } );
	}
</script>
