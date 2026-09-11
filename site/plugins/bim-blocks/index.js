panel.plugin("bim/bim-blocks", {
  blocks: {
    faq: `
      <div @dblclick="open">
        <div v-if="content.faq.length">
          <details v-for="(item, index) in content.faq" class="k-block-type-faq-item" :key="index">
            <summary>{{ item.question }}</summary>
            <div v-html="item.answer"></div>
          </details>
        </div>
        <div v-else>No questions yet</div>
      </div>
    `,
    largeText: {
      computed: {
        textField() {
          return this.field("text", {});
        }
      },
      methods: {
        focus() {
          this.$refs.input.focus();
        }
      },
      template: `
        <div :class="'k-block-type-box box-' + content.boxtype">
          <k-writer-input
            ref="input"
            class="k-block-type-text-input"
            :disabled="disabled"
            :value="content.text"
            v-bind="textField"
            @input="update({ text: $event })"
          />
          <k-icon
            v-if="content.boxtype !== 'neutral'"
            class="k-block-type-box-icon"
            :type="content.boxtype"
          />
        </div>
      `
    }
  }
});