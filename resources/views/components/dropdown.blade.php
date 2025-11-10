@props([
  'align' => 'right',
  'width' => '48',
  'contentClasses' => 'py-1 bg-white',
  'closeOnClick' => true,
])

@php
  $widthClass = $width === '48' ? 'w-48' : $width;
@endphp

<div
  class="relative"
  x-data="{
    open:false,
    justOpened:false,
    style:'top:0;left:0',
    align:'{{ $align }}',
    closeOnClick: {{ $closeOnClick ? 'true' : 'false' }},

    updatePos(trigger, menu){
      const r = trigger.getBoundingClientRect();

      menu.style.visibility='hidden';
      menu.style.display='block';
      const mw = menu.offsetWidth;
      const mh = menu.offsetHeight;
      menu.style.display='none';
      menu.style.visibility='';

      const vw = window.innerWidth, vh = window.innerHeight;

      const below = r.bottom + 8;
      const above = Math.max(8, r.top - mh - 8);
      const top = (vh - below >= mh) ? below : above;

      let left;
      if (this.align === 'left') {
        left = Math.min(Math.max(8, r.left), vw - mw - 8);
      } else {
        left = Math.min(Math.max(8, r.right - mw), vw - mw - 8);
      }

      this.style = `top:${top}px;left:${left}px`;
    },

    openMenu(){
      if (this.open) return;

      window.dispatchEvent(new CustomEvent('close-all-dropdowns', { detail: { except: this.$root } }));

      this.open = true;
      this.justOpened = true;
      requestAnimationFrame(()=>{
        this.updatePos(this.$refs.trigger, this.$refs.menu);
        setTimeout(()=> this.justOpened = false, 0);
      });
    },
    closeMenu(){ this.open = false; }
  }"

  @keydown.escape.window="closeMenu()"

  @close-all-dropdowns.window="if ($event.detail?.except !== $root) closeMenu()"
>
  <div x-ref="trigger" @click.stop="open ? closeMenu() : openMenu()">
    {{ $trigger }}
  </div>

  <template x-teleport="body">
    <div
      x-ref="menu"
      x-show="open"
      @click.outside="
        if (justOpened) return;
        closeMenu()
      "
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-75"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="fixed z-[1000] {{ $widthClass }} rounded-md shadow-lg"
      :style="style"
      style="display:none"
      @click="if (closeOnClick) closeMenu()"
    >
      <div class="rounded-md ring-1 ring-black/10 max-h-[calc(100vh-3rem)] overflow-auto {{ $contentClasses }}">
        {{ $content }}
      </div>
    </div>
  </template>
</div>
