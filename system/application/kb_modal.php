<?php
require_once SYSTEMPATH .'body.php';
require_once __DIR__ .'/kb_common.php';
?>

<link href="<?= ASSETS_URL ?>sections/kb.css" type="text/css" rel="stylesheet" />

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

    function ckeditCheck( $target ) {
            console.assert( $target, "ERROR: didn't specify target to check!")
            console.assert( $target.attr('id'), "ERROR: can't find target's id!")
        return $target.attr('id').startsWith('cke_');
    }
    function ckeditRealEdit( $target ) {
        if( $target.length > 1 ) { //;debugger;
            $target.each( (idx,_target) => {
                const $t = $(_target)
                if( $t.hasClass('cke_contents') ) {
                    $target = $t; return false;
                }
            } )
        }
        const $result = $target.parent().parent().siblings('textarea') // TODO figure a nicer way? This won't work in CKEdit v5
            console.assert( $result, "ERROR: can't find the specified CKEDITOR target!")
        const editor = CKEDITOR.instances[ $result.attr('id') ]
            console.assert( editor, "ERROR: can't find the specified CKEDITOR instance!")
        return { $edit: $result, instance: editor };
    }

    function insertTemplate( target, text ) {
        const $target = $(target)
            console.assert($target && $target.length, "ERROR: can't find the specified target!")

        const _text = trim( $target.val() )

        if( ckeditCheck($target) ) { // Special case: CKEditor rich controls
            const {$edit:$real, instance:editor} = ckeditRealEdit( $target )

            // const range = editor.createRange()
            // range.moveToElementEditEnd( range.root )
            // editor.getSelection().selectRanges( [range] )
            // editor.insertText( `  ${text}`, null, range )

            editor.insertText( ` ${text}` )
            $target.trigger('input')
            return;
        }
        $target
            .val( trim( _text + " " + text ) )
            .trigger('input')
    }
    function insertTemplateHere( text ) { //debugger;
        const target = globalThis['focused_kb_target']
        if( !target ) {
            console.assert( target, `target not specified!!`, {text} ); return;
        }
        insertTemplate( target, text )
    }
    function _glowTarget( event = "mouseleave", selector = 'textarea.glowing', ev ) {
        //console.log('_event', event)
        if( event == "mouseleave" ) {
            const $old = $(selector),
                  $target = $(ev.target);
            if( $old.length ) {
                $old.removeClass('glowing')
                    // $old.each( (idx,el) => {
                    //     console.log( $(el).attr('id'), "unglowed" )
                    // } )
            }
        } else {
            const target = globalThis['focused_kb_target'];
            if( target ) {
                const $target = $(target);
                if( $target.length ) {
                    $target.addClass('glowing')
                        //console.log( $target.attr('id'), "glowed" )
                    $target[0].scrollIntoView({behavior: "auto", block: "center", inline: "nearest"})
                }
            }
        }
    }

    DefinitionPanel = {
      dockSide: `<?= DOCK_SIDE ?>`,
      area_id: <?= KB_AREA_DEFINITIONS ?>,
      toggler: `#btn-definitions`,
      actions: `.btn-add-definition`,
      targets: `form.--form-SROGS #cke_instruction .cke_contents,
                form.--form-RFAs  #cke_instruction .cke_contents,
                form.--form-RPDs  #cke_instruction .cke_contents,
                textarea[name*="question_titles["]`,
    }

    ObjectionPanel = {
      dockSide: `<?= DOCK_SIDE ?>`,
      area_id: <?= KB_AREA_OBJECTION_TEMPLATES ?>,
      toggler: `#btn-objections`,
      actions: `.btn-add-objection`,
      targets: `textarea[name*="objection["]`,
    }

    ObjectionKillerPanel = {
      dockSide: `<?= DOCK_SIDE ?>`,
      area_id: <?= KB_AREA_OBJECTION_KILLERS ?>,
      toggler: `#btn-objection-killers`,
      actions: `.btn-add-objection-killer`,
      targets: `textarea.er-mc-meet-confer-body`,
    }

    function updateKBEvents() {
        $(`.sidebar > .fixed`).each( (idx, el) => {
            const $el = $(el),
                  { panel, } = $el.data('kb') || {panel:null}
            if( panel ) {
                const $textboxes = $(panel.targets)
                globalThis['focused_kb_target'] = $textboxes.first()
                    //console.log(`target:first = ` + $textboxes.first().attr('id') )

                $textboxes.on('focus', ev => {
                    const _target = `#${ev.target.id}`
                    globalThis['focused_kb_target'] = _target;
                        //console.log(`target:focused = ${_target}`)
                } )

                // TODO this won't work if there's more than one CKEditor in the targets, if that day arrives, we'll need a more general solution
                const {$edit:$real, instance:editor} = ckeditRealEdit( $textboxes )
                if( editor ) {
                    editor.on('focus', ev => { //;debugger;
                        const _target = '#'+$(ev.sender.ui.contentsElement).attr('id')
                        globalThis['focused_kb_target'] = _target;
                            //console.log(`editor:focused = ${_target}`)
                    } )
                }
            }
        } )
    }
    function updateKBSidebar( form_id, panel ) {
        $.post( `<?= ROOTURL ?>system/application/kb.php?area=${panel.area_id}&section_filter=${form_id}` )
            .done( data => {
                const $sidebar = $(`.sidebar.${panel.dockSide} > .fixed`),
                      $sidebar_items = $sidebar.find(`> *`)
                if( $sidebar_items.length < 1 || !$sidebar.data('kb') || !$sidebar.data('kb').section != form_id ) {
                    $sidebar.html(data).data( 'kb', {panel, section: form_id} )
                    updateKBEvents()
                }
                $(panel.actions)
                    .on('mouseenter', ev => _glowTarget( 'mouseenter' ) )
                    .on('mouseleave', ev => _glowTarget( 'mouseleave', `${panel.targets}.glowing`, ev ) )
            } )
    }
    function closeSidebar(aSide = `<?= DOCK_SIDE ?>`) {
        $(`.sidebar.${aSide}`).removeClass('open')
        $(`.sidebar.${aSide} > .fixed`).removeData('kb')
    }
    function toggleKBSidebar( form_id, panel, value="auto" ) { //debugger;
        console.assert( panel && panel.dockSide, 'This needs a SomePanel literal, see examples above...' )
        const $sidebar = $(`.sidebar.${panel.dockSide}`)
        switch( value ) {
            case "auto": case null: case undefined: // just toggle
                $sidebar.toggleClass("open"); break
            case "on": case true:
                $sidebar.addClass("open"); break
            case "off": case false:
                $sidebar.removeClass("open")
        }
        const willOpen = $sidebar.hasClass("open")
        $(panel.toggler).toggleClass( "open", willOpen )
        if( willOpen ) {
            updateKBSidebar( form_id, panel )
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
