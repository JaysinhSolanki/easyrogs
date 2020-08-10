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
#btn-objections > span:before {
    font: inherit;
    content: " Show ";
}
#btn-objections.open > span:before {
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
    padding: 0 0.5em;
}
.sidebar .kb-item>h4 {
    font-weight: 600;
    margin: 0 auto 0.3rem;
    padding: 0 0.5em;
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
    function insertObjectionTemplate( target, text ) { 
        const $target = $(target)
        console.assert($target.length, "ERROR: can't find the specified target!")

        const _text = trim( $target.val() )

        $target.val( trim( _text + " " + text ) )
    }
    function insertObjectionTemplateHere( text ) { 
        const target = globalThis['focused_objection']
        if( !target ) {
            console.log( `target not specified!!`, {text} ); return;
        }
        insertObjectionTemplate( target, text )
    }
    function _glowTarget( event = "mouseleave" ) {
        if( event == "mouseleave" ) {
            const $old = $('textarea.glowing[name*="objection["]')
            if( $old.length ) {
                $old.removeClass('glowing')
                //console.log( $old.id, "unglowed" )
            }
        } else {
            const target = globalThis['focused_objection'];
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
    function showObjectionTemplates( form_id, target="" ) { 
        const $sidebar = $(`.sidebar.<?= DOCK_SIDE ?>`).toggleClass("open"),
              isOpen = $sidebar.hasClass("open")
        $("#btn-objections").toggleClass( "open", isOpen )
        if( isOpen ) {
            $.post( `<?= ROOTURL ?>system/application/kb.php?area=<?= KB_AREA_OBJECTION_TEMPLATES ?>&section_filter=${form_id}` )
                .done( data => { 
                    $sidebar_objections = $(`.sidebar.<?= DOCK_SIDE ?> > .fixed>*`)
                    if( $sidebar_objections.length < 1 || $sidebar_objections.data('section') != form_id ) {
                        $(`.sidebar.<?= DOCK_SIDE ?> > .fixed`).html(data).data('section', form_id)

                        $('.btn-add-objection')
                            .on('mouseenter', ev => _glowTarget( 'mouseenter' ) )
                            .on('mouseleave', ev => _glowTarget( 'mouseleave' ) )
                    }
                    const $textboxes = $('textarea[name*="objection["]')
                    globalThis['focused_objection'] = target || $textboxes.first()
                    $textboxes.on('focus', ev => {
                        const _target = `#${ev.target.id}`
                        globalThis['focused_objection'] = _target;
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
