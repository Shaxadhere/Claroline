import {Cloze as component} from './editor.jsx'
import {makeActionCreator, makeId} from './../../utils/utils'
import cloneDeep from 'lodash/cloneDeep'
import {ITEM_CREATE} from './../../quiz/editor/actions'
import {utils} from './utils/utils'
import {select} from './selectors'
import {notBlank} from './../../utils/validate'
import set from 'lodash/set'
import get from 'lodash/get'
import invariant from 'invariant'
import {tex} from './../../utils/translate'

const UPDATE_TEXT = 'UPDATE_TEXT'
const ADD_HOLE = 'ADD_HOLE'
const OPEN_HOLE = 'OPEN_HOLE'
const UPDATE_HOLE = 'UPDATE_HOLE'
const ADD_ANSWER = 'ADD_ANSWER'
const UPDATE_ANSWER = 'UPDATE_ANSWER'
const SAVE_HOLE = 'SAVE_HOLE'
const REMOVE_HOLE = 'REMOVE_HOLE'
const REMOVE_ANSWER = 'REMOVE_ANSWER'
const CLOSE_POPOVER = 'CLOSE_POPOVER'

export const actions = {
  updateText: makeActionCreator(UPDATE_TEXT, 'text'),
  addHole: makeActionCreator(ADD_HOLE, 'word', 'cb'),
  openHole: makeActionCreator(OPEN_HOLE, 'holeId'),
  updateHole: makeActionCreator(UPDATE_HOLE, 'holeId', 'parameter', 'value'),
  addAnswer: makeActionCreator(ADD_ANSWER, 'holeId'),
  saveHole: makeActionCreator(SAVE_HOLE),
  removeHole: makeActionCreator(REMOVE_HOLE, 'holeId'),
  removeAnswer: makeActionCreator(REMOVE_ANSWER, '_id'),
  closePopover: makeActionCreator(CLOSE_POPOVER),
  updateAnswer: (holeId, parameter, _id, value) => {
    invariant(
      ['text', 'caseSensitive', 'feedback', 'score'].indexOf(parameter) > -1,
      'answer attribute is not valid'
    )
    invariant(holeId !== undefined, 'holeId is required')
    invariant(_id !== undefined, '_id is required')

    return {
      type: UPDATE_ANSWER,
      holeId, parameter, _id, value
    }
  }
}

export default {
  component,
  reduce,
  validate,
  decorate
}

function decorate(item) {
  const solutions = cloneDeep(item.solutions)
  solutions.forEach(solution => solution.answers.forEach(answer => answer._id = makeId()))

  return Object.assign({}, item, {
    _text: utils.setEditorHtml(item.text, item.holes, item.solutions),
    solutions
  })
}

function reduce(item = {}, action) {
  switch (action.type) {
    case ITEM_CREATE: {
      return Object.assign({}, item, {
        text: '',
        holes: [],
        solutions: [],
        _text: ''
      })
    }
    case UPDATE_TEXT: {
      item = Object.assign({}, item, {
        text: utils.getTextWithPlacerHoldersFromHtml(action.text),
        _text: action.text
      })

      const holesToRemove = []
      //we need to check if every hole is mapped to a placeholder
      //if there is not placeholder, then remove the hole
      item.holes.forEach(hole => {
        if (item.text.indexOf(`[[${hole.id}]]`) < 0) {
          holesToRemove.push(hole.id)
        }
      })

      if (holesToRemove) {
        const holes = cloneDeep(item.holes)
        const solutions = cloneDeep(item.solutions)
        holesToRemove.forEach(toRemove => {
          holes.splice(holes.findIndex(hole => hole.id === toRemove), 1)
          solutions.splice(solutions.findIndex(solution => solution.holeId === toRemove), 1)
        })
        item = Object.assign({}, item, {holes, solutions})
      }

      return item
    }
    case OPEN_HOLE: {
      const newItem = cloneDeep(item)
      const hole = getHoleFromId(newItem, action.holeId)
      hole._multiple = !!hole.choices
      newItem._popover = true
      newItem._holeId = action.holeId

      return newItem
    }
    case UPDATE_ANSWER: {
      const newItem = cloneDeep(item)
      const hole = getHoleFromId(newItem, newItem._holeId)
      const solution = getSolutionFromHole(newItem, hole)
      const answer = solution.answers.find(answer => answer._id === action._id)

      answer[action.parameter] = action.value

      updateHoleChoices(hole, solution)

      return newItem
    }
    case ADD_ANSWER: {
      const newItem = cloneDeep(item)
      const hole = getHoleFromId(newItem, newItem._holeId)
      const solution = getSolutionFromHole(newItem, hole)

      solution.answers.push({
        text: '',
        caseSensitive: false,
        feedback: '',
        score: 1,
        _id: makeId()
      })

      updateHoleChoices(hole, solution)

      return newItem
    }
    case UPDATE_HOLE: {
      const newItem = cloneDeep(item)
      const hole = getHoleFromId(newItem, newItem._holeId)

      if (['size', '_multiple'].indexOf(action.parameter) > -1) {
        hole[action.parameter] = action.value
      } else {
        throw `${action.parameter} is not a valid hole attribute`
      }

      updateHoleChoices(hole, getSolutionFromHole(newItem, hole))

      return newItem
    }
    case ADD_HOLE: {
      const newItem = cloneDeep(item)

      const hole = {
        id: makeId(),
        feedback: '',
        size: 10,
        _score: 0,
        _multiple: false,
        placeholder: ''
      }

      const solution = {
        holeId: hole.id,
        answers: [{
          text: action.word,
          caseSensitive: false,
          feedback: '',
          score: 1,
          _id: makeId()
        }]
      }

      newItem.holes.push(hole)
      newItem.solutions.push(solution)
      newItem._popover = true
      newItem._holeId = hole.id
      newItem._text = action.cb(utils.makeTinyHtml(hole, solution))
      newItem.text = utils.getTextWithPlacerHoldersFromHtml(newItem._text)

      return newItem
    }
    case REMOVE_HOLE: {
      const newItem = cloneDeep(item)
      const holes = newItem.holes
      const solutions = newItem.solutions

      // Remove from holes list
      holes.splice(holes.findIndex(hole => hole.id === action.holeId), 1)

      // Remove from solutions
      const solution = solutions.splice(solutions.findIndex(solution => solution.holeId === action.holeId), 1)

      let bestAnswer
      if (solution && 0 !== solution.length) {
        // Retrieve the best answer
        bestAnswer = select.getBestAnswer(solution[0].answers)
      }

      // Replace hole with the best answer text
      const regex = new RegExp(`(\\[\\[${action.holeId}\\]\\])`, 'gi')
      newItem.text = newItem.text.replace(regex, bestAnswer ? bestAnswer.text : '')
      newItem._text = utils.setEditorHtml(newItem.text, newItem.holes, newItem.solutions)

      if (newItem._holeId && newItem._holeId === action.holeId) {
        newItem._popover = false
      }

      return newItem
    }
    case REMOVE_ANSWER: {
      const newItem = cloneDeep(item)
      const hole = getHoleFromId(newItem, item._holeId)
      const solution = getSolutionFromHole(newItem, hole)
      const answers = solution.answers
      answers.splice(answers.findIndex(answer => answer._id === action._id), 1)

      updateHoleChoices(hole, solution)

      return newItem
    }
    case CLOSE_POPOVER: {
      const newItem = cloneDeep(item)
      newItem._popover = false

      return newItem
    }
  }
}

function updateHoleChoices(hole, holeSolution) {
  if (hole._multiple) {
    hole.choices = holeSolution.answers.map(answer => answer.text)
  } else {
    delete hole.choices
  }
}

function getHoleFromId(item, holeId) {
  return item.holes.find(hole => hole.id === holeId)
}

function getSolutionFromHole(item, hole)
{
  return item.solutions.find(solution => solution.holeId === hole.id)
}

function validate(item) {
  const _errors = {}

  item.holes.forEach(hole => {
    const solution = getSolutionFromHole(item, hole)
    let hasPositiveValue = false

    solution.answers.forEach((answer) => {
      if (notBlank(answer.text, true)) {
        set(_errors, 'answers.text', tex('cloze_empty_word_error'))
      }

      if (notBlank(answer.score, true) && answer.score !== 0) {
        set(_errors, 'answers.score', tex('cloze_empty_score_error'))
      }

      if (answer.score > 0) hasPositiveValue = true
    })

    if (hasDuplicates(solution.answers)) {
      set(_errors, 'answers.duplicate', tex('cloze_duplicate_answers'))
    }

    if (!hasPositiveValue) {
      set(_errors, 'answers.value', tex('cloze_solutions_requires_positive_answer'))
    }

    if (hole._multiple && solution.answers.length < 2) {
      set(_errors, 'answers.multiple', tex('cloze_multiple_answers_required'))
    }

    if (notBlank(hole.size, true)) {
      set(_errors, 'answers.size', tex('cloze_empty_size_error'))
    }

    if (!_errors.text) {
      const answerErrors = get(_errors, 'answers.answer')
      if (answerErrors && answerErrors.length > 0) {
        _errors.text = tex('cloze_holes_errors')
      }
    }
  })

  if (notBlank(item.text, true)) {
    _errors.text = tex('cloze_empty_text_error')
  }

  if (!_errors.text) {
    if (item.holes.length === 0) {
      _errors.text = tex('cloze_must_contains_clozes_error')
    }
  }

  return _errors
}

function hasDuplicates(answers) {
  let hasDuplicates = false
  answers.forEach(answer => {
    let count = 0
    answers.forEach(check => {
      if (answer.text === check.text && answer.caseSensitive === check.caseSensitive) {
        count++
      }
    })
    if (count > 1) hasDuplicates = true
  })

  return hasDuplicates
}